<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class GeneratePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('hr');
    }

    public function rules(): array
    {
        return [
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2020,2035',
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => 'Bulan periode penggajian wajib dipilih.',
            'month.between' => 'Bulan harus berupa angka 1 sampai 12.',
            'year.required' => 'Tahun periode penggajian wajib diisi.',
            'year.between' => 'Tahun penggajian tidak valid.',
        ];
    }
}
