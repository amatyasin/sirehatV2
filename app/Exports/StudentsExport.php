<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class StudentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query(): Builder
    {
        $user = auth()->user();

        $query = Student::query()
            ->with(['activeClassHistory.schoolClass', 'activeClassHistory.academicYear', 'school']);

        // Scope by role
        if ($user->hasRole('admin_sekolah')) {
            $query->whereHas('activeClassHistory', fn ($q) => $q->where('school_id', $user->school_id));
        } elseif (! $user->hasRole('super_admin') && ! $user->hasRole('admin_dinkes')) {
            $query->where('instansi_id', $user->instansi_id);
        }

        // Apply filters
        if (! empty($this->filters['school_id'])) {
            $query->whereHas(
                'activeClassHistory',
                fn ($q) => $q->where('school_id', $this->filters['school_id'])
            );
        }

        if (! empty($this->filters['academic_year_id'])) {
            $query->whereHas(
                'activeClassHistory',
                fn ($q) => $q->where('academic_year_id', $this->filters['academic_year_id'])
            );
        }

        if (! empty($this->filters['semester'])) {
            $query->whereHas(
                'activeClassHistory',
                fn ($q) => $q->where('semester', $this->filters['semester'])
            );
        }

        if (! empty($this->filters['school_class_id'])) {
            $query->whereHas(
                'activeClassHistory',
                fn ($q) => $q->where('school_class_id', $this->filters['school_class_id'])
            );
        }

        if (! empty($this->filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $this->filters['jenis_kelamin']);
        }

        if (isset($this->filters['aktif']) && $this->filters['aktif'] !== '') {
            $query->where('aktif', (bool) $this->filters['aktif']);
        }

        return $query->orderBy('nama_lengkap');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'NISN',
            'NIK',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Sekolah',
            'Kelas',
            'Tahun Ajaran',
            'Semester',
            'Status',
            'Nama Orang Tua / Wali',
            'NIK Orang Tua / Wali',
            'No HP Orang Tua / Wali',
        ];
    }

    protected int $rowNumber = 0;

    public function map($student): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $student->nama_lengkap,
            $student->nisn,
            $student->nik ?? '-',
            $student->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $student->tempat_lahir ?? '-',
            $student->tanggal_lahir?->format('d/m/Y') ?? '-',
            $student->alamat ?? '-',
            $student->school?->nama_sekolah ?? '-',
            $student->activeClassHistory?->schoolClass?->nama_kelas ?? '-',
            $student->activeClassHistory?->academicYear?->nama ?? '-',
            $student->activeClassHistory?->semester ?? '-',
            $student->aktif ? 'Aktif' : 'Tidak Aktif',
            $student->nama_orang_tua ?? '-',
            $student->nik_orang_tua ?? '-',
            $student->no_hp_orang_tua ?? '-',
        ];
    }
}
