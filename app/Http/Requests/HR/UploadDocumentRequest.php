<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('hr') ?? false;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'in:KTP,NPWP,CV,CONTRACT'],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }
}
