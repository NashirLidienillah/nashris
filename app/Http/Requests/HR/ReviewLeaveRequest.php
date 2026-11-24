<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class ReviewLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('hr');
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject',
            'rejection_note' => 'required_if:action,reject|nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Tindakan review wajib ditentukan (approve atau reject).',
            'rejection_note.required_if' => 'Catatan alasan penolakan wajib diisi jika menolak pengajuan.',
        ];
    }
}
