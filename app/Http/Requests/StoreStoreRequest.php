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
        
        $rubros = array_keys(config('shopping.store_rubros', []));

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
                'in:' . implode(',', $rubros)
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048' // 2MB max
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
            'logo.image' => 'The logo must be an image file.',
            'logo.mimes' => 'The logo must be a JPEG, PNG, or GIF file.',
            'logo.max' => 'The logo file size must not exceed 2MB.',
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
            'logo' => 'store logo'
        ];
    }
}
