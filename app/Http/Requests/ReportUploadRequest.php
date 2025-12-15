<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportUploadRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,csv,xls|max:10240', // Max 10MB
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'file.required' => 'File harus diunggah',
            'file.file' => 'Upload harus berupa file',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls, atau .csv)',
            'file.max' => 'Ukuran file maksimal 10MB',
        ];
    }
}
