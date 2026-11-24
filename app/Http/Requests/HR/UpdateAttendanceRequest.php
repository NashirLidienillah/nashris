<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('hr');
    }

    public function rules(): array
    {
        return [
            'clock_in' => 'nullable|string',
            'clock_out' => 'nullable|string',
            'late_minutes' => 'required|integer|min:0',
            'status' => 'required|in:Present,Late,Absent,On Leave',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'late_minutes.required' => 'Durasi menit keterlambatan wajib diisi.',
            'status.required' => 'Status kehadiran wajib dipilih.',
        ];
    }
}
