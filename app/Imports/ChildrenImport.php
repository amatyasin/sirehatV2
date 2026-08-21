<?php

namespace App\Imports;

use App\Models\Child;
use App\Models\OrangTua;
use App\Models\Posyandu;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ChildrenImport implements ToCollection, WithHeadingRow
{
    protected $instansiId;

    protected $posyanduId;

    protected $insertedCount = 0;
    protected $skippedCount = 0;

    public function __construct($instansiId, $posyanduId)
    {
        $this->instansiId = $instansiId;
        $this->posyanduId = $posyanduId;
    }

    public function getInsertedCount()
    {
        return $this->insertedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    public function collection(Collection $rows)
    {
        if ($rows->count() === 0) {
            throw new \Exception('File excel kosong.');
        }

        $posyandu = Posyandu::findOrFail($this->posyanduId);
        abort_unless($posyandu->instansi_id === $this->instansiId, 403);

        foreach ($rows as $row) {
            try {
                $row = collect($row)->mapWithKeys(fn ($value, $key) => [
                    strtolower(trim($key)) => $value,
                ]);

                // Wajib ada nama anak
                if (empty($row['nama_lengkap_anak'])) {
                    $this->skippedCount++;
                    continue;
                }

                $validator = Validator::make(
                    $row->toArray(),
                    [
                        'nama_lengkap_anak' => 'required|string|max:255',
                        'jk' => 'required|in:L,P',
                    ]
                );

                if ($validator->fails()) {
                    $this->skippedCount++;
                    continue;
                }

                $tanggalLahirOrtu = null;
                if (! empty($row['tanggal_lahir_ortu'])) {
                    try {
                        $tanggalLahirOrtu = is_numeric($row['tanggal_lahir_ortu'])
                            ? Date::excelToDateTimeObject($row['tanggal_lahir_ortu'])->format('Y-m-d')
                            : Carbon::parse($row['tanggal_lahir_ortu'])->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $tanggalLahirOrtu = null;
                    }
                }

                $tanggalLahirAnak = null;
                if (! empty($row['tanggal_lahir_anak'])) {
                    try {
                        $tanggalLahirAnak = is_numeric($row['tanggal_lahir_anak'])
                            ? Date::excelToDateTimeObject($row['tanggal_lahir_anak'])->format('Y-m-d')
                            : Carbon::parse($row['tanggal_lahir_anak'])->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $tanggalLahirAnak = null;
                    }
                }

                DB::beginTransaction();

                $orangTuaQuery = OrangTua::query();
                if (! empty($row['nik_ortu'])) {
                    $orangTuaQuery->where('nik', $row['nik_ortu']);
                } else {
                    $orangTuaQuery->where('nama_lengkap', $row['nama_lengkap_ortu'])
                        ->where('no_wa', $row['no_wa'] ?? null);
                }

                $orangTua = $orangTuaQuery->first();
                if (! $orangTua) {
                    $orangTua = OrangTua::create([
                        'instansi_id' => $this->instansiId,
                        'posyandu_id' => $this->posyanduId,
                        'nama_lengkap' => $row['nama_lengkap_ortu'] ?? null,
                        'nik' => $row['nik_ortu'] ?? null,
                        'tanggal_lahir' => $tanggalLahirOrtu,
                        'no_wa' => $row['no_wa'] ?? null,
                        'alamat' => $row['alamat'] ?? null,
                    ]);
                }

                $exists = Child::query()
                    ->when(! empty($row['nik_anak']), fn ($query) => $query->where('nik', $row['nik_anak']))
                    ->when(empty($row['nik_anak']), fn ($query) => $query->where('nama_lengkap', $row['nama_lengkap_anak'])
                        ->where('tanggal_lahir', $tanggalLahirAnak)
                    )->exists();

                if ($exists) {
                    DB::rollBack();
                    $this->skippedCount++;
                    continue;
                }

                Child::create([
                    'instansi_id' => $this->instansiId,
                    'posyandu_id' => $this->posyanduId,
                    'orang_tua_id' => $orangTua->id,
                    'nama_lengkap' => $row['nama_lengkap_anak'] ?? null,
                    'nik' => $row['nik_anak'] ?? null,
                    'jenis_kelamin' => $row['jk'] ?? null,
                    'tanggal_lahir' => $tanggalLahirAnak,
                    'alamat' => $row['alamat'] ?? null,
                    'aktif' => true,
                ]);

                DB::commit();
                $this->insertedCount++;
            } catch (\Throwable $e) {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
                $this->skippedCount++;
                continue;
            }
        }

        if ($this->insertedCount === 0) {
            throw new \Exception('Tidak ada data yang berhasil diimport. Pastikan format sesuai.');
        }
    }
}
