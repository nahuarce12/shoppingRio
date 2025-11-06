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
        $tipoUsuario = $request->input('tipo_usuario');
        $isStoreOwner = $tipoUsuario === 'dueño de local';

        $rules = [
            'tipo_usuario' => ['required', 'in:cliente,dueño de local'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($isStoreOwner) {
            $rules = array_merge($rules, [
                'owner_name' => ['required', 'string', 'max:255'],
                'owner_lastname' => ['required', 'string', 'max:255'],
                'owner_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email'],
            ]);
        } else {
            $rules = array_merge($rules, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            ]);
        }

        $validated = $request->validate($rules, [], [
            'owner_name' => 'nombre',
            'owner_lastname' => 'apellido',
            'owner_email' => 'correo electrónico',
        ]);

        $name = $isStoreOwner
            ? trim($validated['owner_name'].' '.$validated['owner_lastname'])
            : $validated['name'];

        $email = $isStoreOwner
            ? $validated['owner_email']
            : $validated['email'];

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'tipo_usuario' => $tipoUsuario,
            'categoria_cliente' => $tipoUsuario === 'cliente' ? 'Inicial' : null,
            'approved_at' => $tipoUsuario === 'cliente' ? now() : null,
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
