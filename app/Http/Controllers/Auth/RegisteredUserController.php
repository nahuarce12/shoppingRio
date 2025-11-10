<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store;
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
        $stores = Store::orderBy('nombre')->get();
        return view('auth.register', compact('stores'));
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($isStoreOwner) {
            $rules['store_id'] = ['required', 'exists:stores,id'];
        }

        $validated = $request->validate($rules, [], [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'store_id' => 'local',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tipo_usuario' => $tipoUsuario,
            'categoria_cliente' => $tipoUsuario === 'cliente' ? 'Inicial' : null,
            'approved_at' => $tipoUsuario === 'cliente' ? now() : null,
            'store_id' => $isStoreOwner ? $validated['store_id'] : null,
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
