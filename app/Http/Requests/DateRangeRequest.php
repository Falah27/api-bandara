<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DateRangeRequest extends FormRequest
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
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:today',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'Tanggal mulai harus diisi',
            'start_date.date' => 'Format tanggal mulai tidak valid',
            'start_date.before_or_equal' => 'Tanggal mulai tidak boleh melebihi hari ini',
            'end_date.required' => 'Tanggal akhir harus diisi',
            'end_date.date' => 'Format tanggal akhir tidak valid',
            'end_date.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
            'end_date.before_or_equal' => 'Tanggal akhir tidak boleh melebihi hari ini',
        ];
    }
}
