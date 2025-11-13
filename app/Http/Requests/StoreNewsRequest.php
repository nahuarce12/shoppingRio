<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Form request for validating news creation and updates.
 * Validates news fields including date ranges and category targeting.
 */
class StoreNewsRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:100'
            ],
            'description' => [
                'required',
                'string',
                'max:200'
            ],
            'imagen' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048' // 2MB max
            ],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:fecha_desde',
                function ($attribute, $value, $fail) {
                    if (!$this->start_date) {
                        return;
                    }
                    
                    $fechaDesde = Carbon::parse($this->start_date);
                    $fechaHasta = Carbon::parse($value);
                    
                    // Check minimum duration (at least 1 day)
                    if ($fechaHasta->isSameDay($fechaDesde)) {
                        // Same day is ok
                        return;
                    }
                    
                    // Check maximum duration from config
                    $maxDuration = config('shopping.news.default_duration_days', 30) * 3; // Allow 3x default
                    if ($fechaHasta->diffInDays($fechaDesde) > $maxDuration) {
                        $fail("The news duration cannot exceed {$maxDuration} days.");
                    }
                }
            ],
            'target_category' => [
                'required',
                'string',
                'in:Inicial,Medium,Premium'
            ],
            'created_by' => [
                'required',
                'integer',
                'exists:users,id'
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
            'texto.required' => 'The news text is required.',
            'texto.max' => 'The news text must not exceed 200 characters.',
            'imagen.image' => 'The image must be an image file.',
            'imagen.mimes' => 'The image must be a JPEG, PNG, or GIF file.',
            'imagen.max' => 'The image file size must not exceed 2MB.',
            'fecha_desde.required' => 'The start date is required.',
            'fecha_desde.date' => 'The start date must be a valid date.',
            'fecha_desde.after_or_equal' => 'The start date must be today or a future date.',
            'fecha_hasta.required' => 'The end date is required.',
            'fecha_hasta.date' => 'The end date must be a valid date.',
            'fecha_hasta.after_or_equal' => 'The end date must be equal to or after the start date.',
            'categoria_destino.required' => 'The target category is required.',
            'categoria_destino.in' => 'The target category must be Inicial, Medium, or Premium.',
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
            'description' => 'news text',
            'imagen' => 'news image',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'target_category' => 'target category'
        ];
    }

    /**
     * Prepare the data for validation.
     * Auto-set created_by to current admin user.
     */
    protected function prepareForValidation(): void
    {
        // Auto-set created_by if not provided
        if (!$this->has('created_by')) {
            $this->merge(['created_by' => Auth::id()]);
        }
    }
}
