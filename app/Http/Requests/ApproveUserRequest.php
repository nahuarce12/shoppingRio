<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

/**
 * Form request for validating store owner approval by admins.
 * Validates that user exists and is pending approval.
 */
class ApproveUserRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->route('user');
        $routeName = optional($this->route())->getName();
        $action = $routeName === 'admin.users.reject' ? 'reject' : 'approve';

        if ($user instanceof User) {
            $this->merge([
                'user_id' => $user->getKey(),
                'action' => $action,
            ]);

            if ($action === 'reject' && !$this->filled('reason')) {
                $this->merge(['reason' => 'Rechazado por el administrador.']);
            }
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by AdminMiddleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    
                    if (!$user) {
                        $fail('The selected user does not exist.');
                        return;
                    }

                    // Check if user is a store owner
                    if ($user->tipo_usuario !== 'dueño de local') {
                        $fail('Only store owners can be approved.');
                        return;
                    }

                    // Check if user is pending approval
                    if ($user->approved_at !== null) {
                        $fail('This store owner has already been approved.');
                        return;
                    }
                }
            ],
            'action' => [
                'sometimes',
                'string',
                'in:approve,reject'
            ],
            'reason' => [
                'nullable',
                'string',
                'max:500'
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
            'user_id.required' => 'A user must be selected.',
            'user_id.integer' => 'Invalid user ID.',
            'user_id.exists' => 'The selected user does not exist.',
            'action.in' => 'The action must be either approve or reject.',
            'reason.required_if' => 'A reason is required when rejecting a store owner.',
            'reason.max' => 'The reason must not exceed 500 characters.',
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
            'user_id' => 'store owner',
            'action' => 'approval action',
            'reason' => 'rejection reason'
        ];
    }
}
