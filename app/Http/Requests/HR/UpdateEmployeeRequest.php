<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('hr') ?? false;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $userId = $employee?->user_id;

        return [
            // User Data
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],

            // Employment Data
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'shift_id' => ['required', 'exists:shifts,id'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'join_date' => ['required', 'date'],
            'contract_type' => ['required', 'in:PKWTT,PKWT,Probation,Internship'],
            'contract_end_date' => ['nullable', 'required_unless:contract_type,PKWTT', 'date', 'after_or_equal:join_date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'allowance_fixed' => ['required', 'numeric', 'min:0'],
        ];
    }
}
