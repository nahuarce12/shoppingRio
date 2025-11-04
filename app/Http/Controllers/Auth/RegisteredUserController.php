<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tipo_usuario' => ['required', 'in:cliente,dueño de local'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_usuario' => $request->tipo_usuario,
            'categoria_cliente' => $request->tipo_usuario === 'cliente' ? 'Inicial' : null,
            'approved_at' => $request->tipo_usuario === 'cliente' ? now() : null,
        ]);

        event(new Registered($user));

        // Only auto-login clients, store owners must wait for approval
        if ($user->tipo_usuario === 'cliente') {
            Auth::login($user);
            return redirect()->route('verification.notice');
        }

        return redirect()->route('login')
            ->with('success', 'Registro exitoso. Su cuenta será revisada por un administrador.');
    }
}
