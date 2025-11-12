<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Promotion;

/**
 * Form request for validating promotion approval/denial by admins.
 * Validates that promotion exists and is pending approval.
 */
class UpdatePromotionStatusRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $promotion = $this->route('promotion');
        $routeName = optional($this->route())->getName();

        if ($promotion instanceof Promotion) {
            $this->merge([
                'promotion_id' => $promotion->getKey(),
                'estado' => $routeName === 'admin.promotions.approve'
                    ? 'aprobada'
                    : ($routeName === 'admin.promotions.deny' ? 'denegada' : $this->input('estado')),
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by PromotionPolicy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'promotion_id' => [
                'required',
                'integer',
                'exists:promotions,id',
                function ($attribute, $value, $fail) {
                    $promotion = Promotion::find($value);
                    
                    if (!$promotion) {
                        $fail('The selected promotion does not exist.');
                        return;
                    }

                    // Check if promotion is pending
                    if ($promotion->estado !== 'pendiente') {
                        $fail('Only pending promotions can be approved or denied.');
                        return;
                    }
                }
            ],
            'estado' => [
                'required',
                'string',
                'in:aprobada,denegada'
            ],
            'admin_notes' => [
                'nullable',
                'string',
                'max:500'
            ],
            'reason' => [
                'nullable',
                'string',
                'max:500',
                'required_if:estado,denegada'
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
            'promotion_id.required' => 'A promotion must be selected.',
            'promotion_id.integer' => 'Invalid promotion ID.',
            'promotion_id.exists' => 'The selected promotion does not exist.',
            'estado.required' => 'The approval status is required.',
            'estado.in' => 'The status must be either approved (aprobada) or denied (denegada).',
            'admin_notes.max' => 'Admin notes must not exceed 500 characters.',
            'reason.required_if' => 'A reason is required when denying a promotion.',
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
            'promotion_id' => 'promotion',
            'estado' => 'approval status',
            'admin_notes' => 'admin notes',
            'reason' => 'denial reason'
        ];
    }
}
