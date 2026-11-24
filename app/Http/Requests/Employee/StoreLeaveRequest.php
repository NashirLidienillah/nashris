<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('employee');
    }

    public function rules(): array
    {
        return [
            'leave_type' => 'required|in:Annual,Sick,Special',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'leave_type.required' => 'Pilih jenis izin / cuti yang diajukan.',
            'start_date.required' => 'Tanggal mulai cuti wajib diisi.',
            'end_date.required' => 'Tanggal selesai cuti wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai cuti tidak boleh mendahului tanggal mulai.',
            'reason.required' => 'Alasan pengajuan cuti wajib dijelaskan secara ringkas.',
            'attachment.max' => 'Ukuran berkas lampiran maksimal 2MB.',
        ];
    }
}
