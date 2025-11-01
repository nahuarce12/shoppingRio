<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\StoreOwnerApproved;
use App\Mail\StoreOwnerRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StoreOwnerApprovalController extends Controller
{
    /**
     * Display a listing of pending store owner registrations.
     */
    public function index()
    {
        $pendingStoreOwners = User::storeOwners()
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.approvals.store-owners', compact('pendingStoreOwners'));
    }

    /**
     * Approve a store owner registration.
     */
    public function approve(User $user)
    {
        // Verify the user is a pending store owner
        if (!$user->isStoreOwner() || $user->isApproved()) {
            return back()->with('error', 'Este usuario no es un dueño de local pendiente de aprobación.');
        }

        // Approve the store owner
        $user->approved_at = now();
        $user->approved_by = Auth::id();
        $user->save();

        // Send approval email notification
        try {
            Mail::to($user->email)->send(new StoreOwnerApproved($user));
        } catch (\Exception $e) {
            Log::error('Failed to send store owner approval email: ' . $e->getMessage());
        }

        return back()->with('success', "El dueño de local {$user->name} ha sido aprobado exitosamente.");
    }

    /**
     * Reject a store owner registration.
     */
    public function reject(Request $request, User $user)
    {
        // Verify the user is a pending store owner
        if (!$user->isStoreOwner() || $user->isApproved()) {
            return back()->with('error', 'Este usuario no es un dueño de local pendiente de aprobación.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $reason = $request->input('reason', 'Su solicitud no cumple con los requisitos necesarios.');

        // Send rejection email notification before deleting
        try {
            Mail::to($user->email)->send(new StoreOwnerRejected($user, $reason));
        } catch (\Exception $e) {
            Log::error('Failed to send store owner rejection email: ' . $e->getMessage());
        }

        // Soft delete the user (rejection)
        $userName = $user->name;
        $user->delete();

        return back()->with('success', "La solicitud de {$userName} ha sido rechazada.");
    }
}
