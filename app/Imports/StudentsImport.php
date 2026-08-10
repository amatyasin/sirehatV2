<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentClassHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentsImport implements ToCollection
{
    protected $academicYearId;

    protected $semester;

    protected $schoolClassId;

    protected $instansiId;

    protected $schoolId;

    public function __construct(
        $instansiId,
        $schoolId,
        $academicYearId,
        $semester,
        $schoolClassId
    ) {
        $this->instansiId = $instansiId;

        $this->schoolId = $schoolId;

        $this->academicYearId = $academicYearId;

        $this->semester = $semester;

        $this->schoolClassId = $schoolClassId;
    }

    public function collection(
        Collection $rows
    ): void {

        DB::transaction(function () use ($rows) {

            foreach ($rows->skip(1) as $row) {

                if (
                    empty($row[0])
                    && empty($row[1])
                ) {
                    continue;
                }

                $nama =
                    trim($row[0] ?? '');

                $nisn =
                    trim($row[1] ?? '');

                $jenisKelamin =
                    strtoupper(
                        trim($row[2] ?? '')
                    );

                $alamat =
                    trim($row[4] ?? '');

                $nik =
                    trim($row[5] ?? '');

                $tempatLahir =
                    trim($row[6] ?? '');

                $namaOrangTua =
                    trim($row[7] ?? '');

                $nikOrangTua =
                    trim($row[8] ?? '');

                $noHpOrangTua =
                    trim($row[9] ?? '');

                Validator::make(

                    [
                        'nama_lengkap' => $nama,

                        'jenis_kelamin' => $jenisKelamin,

                        'nisn' => $nisn,

                        'nik' => $nik,

                    ],

                    [
                        'nama_lengkap' => 'required|string|max:255',

                        'jenis_kelamin' => 'nullable|in:L,P',

                        'nisn' => 'required|string|max:20',

                        'nik' => 'nullable|string|max:20',

                    ],

                    [
                        'nisn.required' => 'NISN wajib diisi.',
                    ]

                )->validate();

                $tanggalLahir = null;

                if (! empty($row[3])) {

                    try {

                        $tanggalLahir =
                            is_numeric($row[3])

                            ? Date::excelToDateTimeObject(
                                $row[3]
                            )->format('Y-m-d')

                            : Carbon::parse(
                                $row[3]
                            )->format('Y-m-d');

                    } catch (\Throwable $e) {

                        $tanggalLahir = null;
                    }
                }

                $nik =
                    filled($nik)
                        ? $nik
                        : null;

                $tempatLahir =
                    filled($tempatLahir)
                        ? $tempatLahir
                        : null;

                $namaOrangTua =
                    filled($namaOrangTua)
                        ? $namaOrangTua
                        : null;

                $nikOrangTua =
                    filled($nikOrangTua)
                        ? $nikOrangTua
                        : null;

                $noHpOrangTua =
                    filled($noHpOrangTua)
                        ? $noHpOrangTua
                        : null;

                $student =
                    Student::updateOrCreate(

                        [
                            'nisn' => $nisn,
                        ],

                        [
                            'instansi_id' => $this->instansiId,

                            'school_id' => $this->schoolId,

                            'nama_lengkap' => $nama,

                            'nik' => $nik,

                            'jenis_kelamin' => $jenisKelamin,

                            'tempat_lahir' => $tempatLahir,

                            'tanggal_lahir' => $tanggalLahir,

                            'alamat' => $alamat,

                            'nama_orang_tua' => $namaOrangTua,

                            'nik_orang_tua' => $nikOrangTua,

                            'no_hp_orang_tua' => $noHpOrangTua,

                            'aktif' => true,
                        ]

                    );

                StudentClassHistory::where(
                    'student_id',
                    $student->id
                )->update([

                    'aktif' => false,

                ]);

                StudentClassHistory::updateOrCreate(

                    [
                        'student_id' => $student->id,

                        'school_id' => $this->schoolId,

                        'school_class_id' => $this->schoolClassId,

                        'academic_year_id' => $this->academicYearId,

                        'semester' => $this->semester,
                    ],

                    [
                        'aktif' => true,
                    ]

                );
            }
        });
    }
}
