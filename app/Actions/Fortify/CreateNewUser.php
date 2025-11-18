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
            'user_type' => ['required', 'string', Rule::in(['cliente', 'dueño de local'])],
            'store_id' => ['required_if:tipo_usuario,dueño de local', 'nullable', 'exists:stores,id'],
        ])->validate();

        // Determine categoria_cliente - only for 'cliente' tipo_usuario
        $categoria = $input['user_type'] === 'cliente' ? 'Inicial' : null;

        // Store owners require admin approval, clients don't
        $approved_at = $input['user_type'] === 'cliente' ? now() : null;

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'user_type' => $input['user_type'],
            'client_category' => $categoria,
            'approved_at' => $approved_at,
            'store_id' => $input['store_id'] ?? null,
        ]);
    }
}
