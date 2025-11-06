<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Form request for validating promotion creation.
 * Validates promotion fields including date ranges, days of week, and store ownership.
 */
class StorePromotionRequest extends FormRequest
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
            'texto' => [
                'required',
                'string',
                'max:200'
            ],
            'fecha_desde' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'fecha_hasta' => [
                'required',
                'date',
                'after_or_equal:fecha_desde',
                function ($attribute, $value, $fail) {
                    if (!$this->fecha_desde) {
                        return;
                    }
                    
                    $fechaDesde = Carbon::parse($this->fecha_desde);
                    $fechaHasta = Carbon::parse($value);
                    $maxDuration = config('shopping.promotion.max_duration_days', 365);
                    
                    if ($fechaHasta->diffInDays($fechaDesde) > $maxDuration) {
                        $fail("The promotion duration cannot exceed {$maxDuration} days.");
                    }
                }
            ],
            'dias_semana' => [
                'required',
                'array',
                'size:7',
                function ($attribute, $value, $fail) {
                    foreach ($value as $index => $day) {
                        if (!is_bool($day) && !in_array($day, [0, 1, '0', '1', true, false])) {
                            $fail('Each day must be a boolean value.');
                            return;
                        }
                    }
                    
                    // Check if at least one day is true
                    $hasAtLeastOneDay = false;
                    foreach ($value as $day) {
                        if ($day === true || $day === 1 || $day === '1') {
                            $hasAtLeastOneDay = true;
                            break;
                        }
                    }
                    
                    if (!$hasAtLeastOneDay) {
                        $fail('At least one day of the week must be selected.');
                    }
                }
            ],
            'dias_semana.*' => [
                'required',
                'boolean'
            ],
            'categoria_minima' => [
                'required',
                'string',
                'in:Inicial,Medium,Premium'
            ],
            'store_id' => [
                'required',
                'integer',
                'exists:stores,id',
                function ($attribute, $value, $fail) {
                    // Check if user owns this store (for store owners)
                    $user = Auth::user();
                    if (!$user) {
                        $fail('You must be authenticated to create promotions.');
                        return;
                    }

                    // Admins can create promotions for any store
                    if ($user->isAdmin()) {
                        return;
                    }

                    // Store owners can only create promotions for their own stores
                    if ($user->isStoreOwner()) {
                        $store = \App\Models\Store::find($value);
                        if (!$store || $store->owner_id !== $user->id) {
                            $fail('You can only create promotions for your own store.');
                        }
                    } else {
                        $fail('Only store owners and administrators can create promotions.');
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
            'texto.required' => 'The promotion description is required.',
            'texto.max' => 'The promotion description must not exceed 200 characters.',
            'fecha_desde.required' => 'The start date is required.',
            'fecha_desde.date' => 'The start date must be a valid date.',
            'fecha_desde.after_or_equal' => 'The start date must be today or a future date.',
            'fecha_hasta.required' => 'The end date is required.',
            'fecha_hasta.date' => 'The end date must be a valid date.',
            'fecha_hasta.after_or_equal' => 'The end date must be equal to or after the start date.',
            'dias_semana.required' => 'Days of the week are required.',
            'dias_semana.array' => 'Days of the week must be an array.',
            'dias_semana.size' => 'You must specify exactly 7 days (Monday to Sunday).',
            'dias_semana.*.boolean' => 'Each day must be true or false.',
            'categoria_minima.required' => 'The minimum client category is required.',
            'categoria_minima.in' => 'The minimum category must be Inicial, Medium, or Premium.',
            'store_id.required' => 'A store must be selected.',
            'store_id.exists' => 'The selected store does not exist.',
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
            'texto' => 'promotion description',
            'fecha_desde' => 'start date',
            'fecha_hasta' => 'end date',
            'dias_semana' => 'days of the week',
            'categoria_minima' => 'minimum category',
            'store_id' => 'store'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->filled('store_id')) {
            $user = Auth::user();
            if ($user && $user->isStoreOwner()) {
                $storeId = $user->stores()->value('id');
                if ($storeId) {
                    $this->merge(['store_id' => $storeId]);
                }
            }
        }

        // Convert dias_semana values to boolean if they come as strings or integers
        if ($this->has('dias_semana') && is_array($this->dias_semana)) {
            $diasSemana = [];
            foreach ($this->dias_semana as $day) {
                if (is_string($day)) {
                    $diasSemana[] = filter_var($day, FILTER_VALIDATE_BOOLEAN);
                } else {
                    $diasSemana[] = (bool) $day;
                }
            }
            $this->merge(['dias_semana' => $diasSemana]);
        }
    }
}
