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
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
                function ($attribute, $value, $fail) {
                    if (!$this->start_date) {
                        return;
                    }
                    
                    $fechaDesde = Carbon::parse($this->start_date);
                    $fechaHasta = Carbon::parse($value);
                    $maxDuration = config('shopping.promotion.max_duration_days', 365);
                    
                    if ($fechaHasta->diffInDays($fechaDesde) > $maxDuration) {
                        $fail("La duración de la promoción no puede exceder {$maxDuration} días.");
                    }
                }
            ],
            'weekdays' => [
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
                        $fail('Debes seleccionar al menos un día de la semana.');
                    }
                }
            ],
            'minimum_category' => [
                'required',
                'string',
                'in:Inicial,Medium,Premium'
            ],
            'imagen' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048' // 2MB max
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
                        // Cast both to int to avoid type comparison issues
                        if ((int)$user->store_id !== (int)$value) {
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
            'title.required' => 'El título de la promoción es obligatorio.',
            'title.max' => 'El título no debe exceder 100 caracteres.',
            'description.required' => 'La descripción de la promoción es obligatoria.',
            'description.max' => 'La descripción no debe exceder 200 caracteres.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'start_date.after_or_equal' => 'La fecha de inicio debe ser hoy o una fecha futura.',
            'end_date.required' => 'La fecha de finalización es obligatoria.',
            'end_date.date' => 'La fecha de finalización debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio.',
            'weekdays.required' => 'Los días de la semana son obligatorios.',
            'weekdays.array' => 'Los días de la semana deben ser un array.',
            'weekdays.size' => 'Debes especificar exactamente 7 días (de Lunes a Domingo).',
            'minimum_category.required' => 'La categoría mínima de cliente es obligatoria.',
            'minimum_category.in' => 'La categoría mínima debe ser Inicial, Medium o Premium.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser un archivo JPEG, PNG o GIF.',
            'imagen.max' => 'El tamaño de la imagen no debe exceder 2MB.',
            'store_id.required' => 'Debe seleccionar un local.',
            'store_id.exists' => 'El local seleccionado no existe.',
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
            'title' => 'título de la promoción',
            'description' => 'descripción de la promoción',
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha de finalización',
            'weekdays' => 'días de la semana',
            'minimum_category' => 'categoría mínima',
            'imagen' => 'imagen de la promoción',
            'store_id' => 'local'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->filled('store_id')) {
            $user = Auth::user();
            if ($user && $user->isStoreOwner() && $user->store_id) {
                $this->merge(['store_id' => $user->store_id]);
            }
        }

        // Convert dias_semana values to boolean if they come as strings or integers
        if ($this->has('weekdays') && is_array($this->weekdays)) {
            $diasSemana = [];
            foreach ($this->weekdays as $day) {
                if (is_string($day)) {
                    $diasSemana[] = filter_var($day, FILTER_VALIDATE_BOOLEAN);
                } else {
                    $diasSemana[] = (bool) $day;
                }
            }
            $this->merge(['weekdays' => $diasSemana]);
        }
    }
}
