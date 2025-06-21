<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoidPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'void_reason' => 'required|string|min:10|max:500'
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'void_reason.required' => 'Alasan void harus diisi',
            'void_reason.min' => 'Alasan void minimal 10 karakter',
            'void_reason.max' => 'Alasan void maksimal 500 karakter'
        ];
    }
}
