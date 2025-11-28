<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveUserRequest;
use App\Mail\StoreOwnerApproved;
use App\Mail\StoreOwnerRejected;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Admin controller for approving or rejecting store owner registrations.
 * Implements the store owner approval workflow required by business rules.
 */
class UserApprovalController extends Controller
{
    /**
     * Display a listing of pending store owner registrations.
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'dueño de local')
            ->whereNull('approved_at');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Order by registration date (oldest first)
        $query->orderBy('created_at');

        $pendingUsers = $query->paginate(20);

        // Statistics
        $stats = [
            'pending' => User::where('user_type', 'dueño de local')
                ->whereNull('approved_at')
                ->count(),
            'approved_today' => User::where('user_type', 'dueño de local')
                ->whereDate('approved_at', today())
                ->count(),
            'total_approved' => User::where('user_type', 'dueño de local')
                ->whereNotNull('approved_at')
                ->count(),
        ];

        return view('admin.users.approval.index', compact('pendingUsers', 'stats'));
    }

    /**
     * Display the specified user for review.
     */
    public function show(User $user)
    {
        // Only show store owners
        if ($user->user_type !== 'dueño de local') {
            return redirect()
                ->route('admin.users.approval.index')
                ->with('error', 'Este usuario no es dueño de local.');
        }

        // Only show pending users
        if ($user->approved_at !== null) {
            return redirect()
                ->route('admin.users.approval.index')
                ->with('info', 'Este usuario ya fue procesado.');
        }

        return view('admin.users.approval.show', compact('user'));
    }

    /**
     * Approve a store owner registration.
     */
    public function approve(ApproveUserRequest $request, User $user)
    {
        if ($user->user_type !== 'dueño de local') {
            $message = 'Este usuario no es dueño de local.';
            return $request->expectsJson()
                ? response()->json(['error' => $message], 422)
                : redirect()->route('admin.users.approval.index')->with('error', $message);
        }

        if ($user->approved_at !== null) {
            $message = 'Este usuario ya fue aprobado anteriormente.';
            return $request->expectsJson()
                ? response()->json(['error' => $message], 422)
                : redirect()->route('admin.users.approval.index')->with('error', $message);
        }

        try {
            // Approve user
            $user->approved_at = now();
            $user->save();

            Log::info('Store owner approved by admin', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'admin_id' => auth()->id(),
                'approved_at' => $user->approved_at
            ]);

            // Send approval email (in separate try-catch to not fail the whole operation)
            try {
                Mail::to($user->email)->send(new StoreOwnerApproved($user));
                Log::info('Approval email sent successfully', ['user_id' => $user->id]);
            } catch (\Exception $mailException) {
                Log::warning('Failed to send approval email', [
                    'user_id' => $user->id,
                    'error' => $mailException->getMessage(),
                    'trace' => $mailException->getTraceAsString()
                ]);
                // Don't fail the approval even if email fails
            }

            $successMessage = "Usuario '{$user->name}' aprobado exitosamente.";
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'approved_at' => $user->approved_at
                    ]
                ]);
            }

            return redirect()
                ->route('admin.users.approval.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Failed to approve store owner', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);

            $errorMessage = 'Error al aprobar el usuario. Por favor intente nuevamente.';
            
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage], 500);
            }

            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Reject a store owner registration with reason.
     */
    public function reject(ApproveUserRequest $request, User $user)
    {
        if ($user->user_type !== 'dueño de local') {
            return redirect()
                ->route('admin.users.approval.index')
                ->with('error', 'Este usuario no es dueño de local.');
        }

        if ($user->approved_at !== null) {
            return redirect()
                ->route('admin.users.approval.index')
                ->with('error', 'Este usuario ya fue procesado.');
        }

        $reason = $request->validated()['reason'] ?? 'No se especificó un motivo.';

        try {
            // Send rejection email before deleting
            Mail::to($user->email)->send(new StoreOwnerRejected($user, $reason));

            Log::info('Store owner rejected by admin', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'reason' => $reason,
                'admin_id' => auth()->id()
            ]);

            // Delete user account (rejected registrations are not kept)
            $userName = $user->name;
            $user->delete();

            return redirect()
                ->route('admin.users.approval.index')
                ->with('success', "Registro de '{$userName}' rechazado. Se envió notificación con el motivo.");
        } catch (\Exception $e) {
            Log::error('Failed to reject store owner', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al rechazar el usuario. Por favor intente nuevamente.');
        }
    }

    /**
     * Display all store owners (approved and pending).
     */
    public function all(Request $request)
    {
        $query = User::where('user_type', 'dueño de local');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('approved_at');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        $users = $query->paginate(20);

        return view('admin.users.all', compact('users'));
    }
}
