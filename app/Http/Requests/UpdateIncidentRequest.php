<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'severity' => 'sometimes|required|in:critical,high,medium,low',
            'status' => 'sometimes|required|in:open,investigating,resolved,closed',
            'source_ip' => 'sometimes|required|ip',
            'affected_host' => 'sometimes|required|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'severity.in' => 'La severidad debe ser: critical, high, medium, o low.',
            'status.in' => 'El estado debe ser: open, investigating, resolved, o closed.',
            'source_ip.ip' => 'La IP origen debe ser una dirección IP válida.',
            'assigned_to.exists' => 'El usuario asignado no existe.',
        ];
    }
}
