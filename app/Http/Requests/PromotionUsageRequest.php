<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Promotion;
use App\Services\PromotionService;
use Illuminate\Support\Facades\Auth;

/**
 * Form request for validating promotion usage requests from clients.
 * Validates promotion eligibility including approval status, date, day, category, and single-use rule.
 */
class PromotionUsageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        return [
            'promotion_id' => [
                'required',
                'integer',
                'exists:promotions,id',
                function ($attribute, $value, $fail) {
                    $user = Auth::user();
                    if (!$user) {
                        $fail('You must be authenticated to request promotions.');
                        return;
                    }

                    if (!$user->isClient()) {
                        $fail('Only clients can request promotions.');
                        return;
                    }

                    if (!$user->hasVerifiedEmail()) {
                        $fail('You must verify your email before requesting promotions.');
                        return;
                    }

                    $promotion = Promotion::find($value);
                    if (!$promotion) {
                        $fail('The selected promotion does not exist.');
                        return;
                    }

                    // Check if promotion is approved
                    if ($promotion->estado !== 'aprobada') {
                        $fail('This promotion is not approved yet.');
                        return;
                    }

                    // Use PromotionService to check full eligibility
                    $promotionService = new PromotionService();
                    $eligibility = $promotionService->checkEligibility($promotion, $user);

                    if (!$eligibility['eligible']) {
                        $fail($eligibility['reason']);
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
            'promotion_id.required' => 'A promotion must be selected.',
            'promotion_id.integer' => 'Invalid promotion ID.',
            'promotion_id.exists' => 'The selected promotion does not exist.',
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
            'promotion_id' => 'promotion'
        ];
    }
}
