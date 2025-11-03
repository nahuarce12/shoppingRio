<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for validating store creation and updates.
 * Validates store fields according to business rules.
 */
class StoreStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization is handled by StorePolicy, this just returns true.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $storeId = $this->route('store') ? $this->route('store')->id : null;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'unique:stores,nombre,' . $storeId
            ],
            'ubicacion' => [
                'required',
                'string',
                'max:50'
            ],
            'rubro' => [
                'required',
                'string',
                'max:20',
                'in:' . implode(',', array_keys(config('shopping.store_rubros', [])))
            ],
            'owner_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (!$user || $user->tipo_usuario !== 'dueño de local') {
                        $fail('The selected owner must be a store owner.');
                    }
                    if (!$user->isApproved()) {
                        $fail('The selected owner must be approved by an administrator.');
                    }
                }
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'The store name is required.',
            'nombre.max' => 'The store name must not exceed 100 characters.',
            'nombre.unique' => 'A store with this name already exists.',
            'ubicacion.required' => 'The store location is required.',
            'ubicacion.max' => 'The location must not exceed 50 characters.',
            'rubro.required' => 'The business category is required.',
            'rubro.in' => 'The selected business category is invalid.',
            'owner_id.required' => 'A store owner must be assigned.',
            'owner_id.exists' => 'The selected owner does not exist.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'store name',
            'ubicacion' => 'location',
            'rubro' => 'business category',
            'owner_id' => 'store owner'
        ];
    }
}
