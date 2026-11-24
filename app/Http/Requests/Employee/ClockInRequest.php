<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class ClockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('employee');
    }

    public function rules(): array
    {
        return [
            'notes' => 'nullable|string|max:255',
        ];
    }
}
