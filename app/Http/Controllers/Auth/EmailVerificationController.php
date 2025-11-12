<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification notice.
     */
    public function notice(): View
    {
        return view('auth.verify-email');
    }

    /**
     * Handle an incoming email verification request.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            // Redirect based on user type
            return $this->redirectByUserType($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Redirect based on user type after verification
        return $this->redirectByUserType($user)->with('verified', true);
    }
    
    /**
     * Redirect user based on their type after email verification.
     */
    protected function redirectByUserType($user): RedirectResponse
    {
        return match($user->tipo_usuario) {
            'administrador' => redirect()->route('admin.dashboard')->with('success', '¡Email verificado exitosamente!'),
            'dueño de local' => redirect()->route('store.dashboard')->with('success', '¡Email verificado exitosamente!'),
            'cliente' => redirect()->route('client.dashboard')->with('success', '¡Email verificado exitosamente!'),
            default => redirect()->route('home')->with('success', '¡Email verificado exitosamente!')
        };
    }

    /**
     * Resend the email verification notification.
     */
    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('client.dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
