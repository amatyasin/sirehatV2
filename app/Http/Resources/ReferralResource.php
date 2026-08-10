<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $studentHistory = $this->studentClassHistory;
        $student = $studentHistory?->student;
        $school = $studentHistory?->school;
        $class = $studentHistory?->schoolClass;

        return [
            'id' => $this->id,
            'student_class_history_id' => $this->student_class_history_id,
            'pemeriksaan_type' => $this->pemeriksaan_type,
            'pemeriksaan_id' => $this->pemeriksaan_id,
            'jenis_pemeriksaan' => $this->jenis_pemeriksaan,
            'alasan_rujukan' => $this->alasan_rujukan,
            'status_rujukan' => $this->status_rujukan,
            'tujuan_rujukan' => $this->tujuan_rujukan,
            'petugas_pemeriksa' => $this->petugas_pemeriksa,
            'tanggal_pemeriksaan' => $this->tanggal_pemeriksaan?->toDateString(),
            'tanggal_rujukan' => $this->tanggal_rujukan?->toDateString(),
            'catatan_tindak_lanjut' => $this->catatan_tindak_lanjut,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            
            // Student details
            'student' => $student ? [
                'id' => $student->id,
                'nama_lengkap' => $student->nama_lengkap,
                'nik' => $student->nik,
                'nisn' => $student->nisn,
                'jenis_kelamin' => $student->jenis_kelamin,
                'tanggal_lahir' => $student->tanggal_lahir?->toDateString(),
            ] : null,

            // School details
            'school' => $school ? [
                'id' => $school->id,
                'nama_sekolah' => $school->nama_sekolah,
                'npsn' => $school->npsn,
            ] : null,

            // Class details
            'class' => $class ? [
                'id' => $class->id,
                'nama_kelas' => $class->nama_kelas,
            ] : null,

            // History logs
            'status_histories' => $this->relationLoaded('statusHistories') ? $this->statusHistories->map(function ($history) {
                return [
                    'id' => $history->id,
                    'status_lama' => $history->status_lama,
                    'status_baru' => $history->status_baru,
                    'catatan' => $history->catatan,
                    'user' => $history->user ? [
                        'id' => $history->user->id,
                        'name' => $history->user->name,
                        'email' => $history->user->email,
                    ] : null,
                    'created_at' => $history->created_at?->toDateTimeString(),
                ];
            }) : [],
        ];
    }
}
