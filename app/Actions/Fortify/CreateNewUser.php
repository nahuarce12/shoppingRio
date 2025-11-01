<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'tipo_usuario' => ['required', 'string', Rule::in(['cliente', 'dueño de local'])],
        ])->validate();

        // Determine categoria_cliente - only for 'cliente' tipo_usuario
        $categoria = $input['tipo_usuario'] === 'cliente' ? 'Inicial' : null;

        // Store owners require admin approval, clients don't
        $approved_at = $input['tipo_usuario'] === 'cliente' ? now() : null;

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'tipo_usuario' => $input['tipo_usuario'],
            'categoria_cliente' => $categoria,
            'approved_at' => $approved_at,
        ]);
    }
}
