<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:critical,high,medium,low',
            'source_ip' => 'required|ip',
            'affected_host' => 'required|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'severity.required' => 'La severidad es obligatoria.',
            'severity.in' => 'La severidad debe ser: critical, high, medium, o low.',
            'source_ip.required' => 'La IP origen es obligatoria.',
            'source_ip.ip' => 'La IP origen debe ser una dirección IP válida.',
            'affected_host.required' => 'El host afectado es obligatorio.',
            'assigned_to.exists' => 'El usuario asignado no existe.',
        ];
    }
}
