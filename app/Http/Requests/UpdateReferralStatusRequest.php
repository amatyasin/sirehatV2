<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReferralStatusRequest extends FormRequest
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
            'status_rujukan' => [
                'required',
                Rule::in(['Belum Dirujuk', 'Sudah Dirujuk', 'Dalam Tindak Lanjut', 'Selesai']),
            ],
            'catatan' => 'nullable|string|max:5000',
        ];
    }
}
