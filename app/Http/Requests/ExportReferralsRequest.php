<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportReferralsRequest extends FormRequest
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
            'school_id' => 'nullable|integer|exists:schools,id',
            'school_class_id' => 'nullable|integer|exists:school_classes,id',
            'jenjang' => 'nullable|string',
            'kecamatan_id' => 'nullable|integer|exists:kecamatans,id',
            'kelurahan_id' => 'nullable|integer|exists:kelurahans,id',
            'instansi_id' => 'nullable|integer|exists:instansis,id',
            'jenis_pemeriksaan' => 'nullable|string',
            'status_rujukan' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'format' => 'required|string|in:xlsx,csv,pdf',
        ];
    }
}
