<?php

namespace App\Repositories\Referral;

use App\Models\Referral;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EloquentReferralRepository implements ReferralRepositoryInterface
{
    protected function applyFilters($query, array $filters)
    {
        // Filter by school
        if (!empty($filters['school_id'])) {
            $query->whereHas('studentClassHistory', function ($q) use ($filters) {
                $q->where('school_id', $filters['school_id']);
            });
        }

        // Filter by school class
        if (!empty($filters['school_class_id'])) {
            $query->whereHas('studentClassHistory', function ($q) use ($filters) {
                $q->where('school_class_id', $filters['school_class_id']);
            });
        }

        // Filter by kecamatan (wilayah)
        if (!empty($filters['kecamatan_id'])) {
            $query->whereHas('studentClassHistory.school', function ($q) use ($filters) {
                $q->where('kecamatan_id', $filters['kecamatan_id']);
            });
        }

        // Filter by kelurahan (wilayah)
        if (!empty($filters['kelurahan_id'])) {
            $query->whereHas('studentClassHistory.school', function ($q) use ($filters) {
                $q->where('kelurahan_id', $filters['kelurahan_id']);
            });
        }

        // Filter by instansi (Puskesmas)
        if (!empty($filters['instansi_id'])) {
            $query->whereHas('studentClassHistory.school', function ($q) use ($filters) {
                $q->where('instansi_id', $filters['instansi_id']);
            });
        }

        // Filter by jenjang (SD/SMP/SMA)
        if (!empty($filters['jenjang'])) {
            $jenjang = strtolower($filters['jenjang']);
            $query->whereHas('studentClassHistory.school', function ($q) use ($jenjang) {
                if ($jenjang === 'sd') {
                    $q->where(function ($sub) {
                        $sub->where('nama_sekolah', 'like', '%SD%')
                            ->orWhere('nama_sekolah', 'like', '%MI%');
                    });
                } elseif ($jenjang === 'smp') {
                    $q->where(function ($sub) {
                        $sub->where('nama_sekolah', 'like', '%SMP%')
                            ->orWhere('nama_sekolah', 'like', '%MTs%');
                    });
                } elseif ($jenjang === 'sma') {
                    $q->where(function ($sub) {
                        $sub->where('nama_sekolah', 'like', '%SMA%')
                            ->orWhere('nama_sekolah', 'like', '%SMK%')
                            ->orWhere('nama_sekolah', 'like', '%MA%');
                    });
                }
            });
        }

        // Filter by type of checkup (jenis_pemeriksaan)
        if (!empty($filters['jenis_pemeriksaan'])) {
            if (is_array($filters['jenis_pemeriksaan'])) {
                $query->whereIn('jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
            } else {
                $query->where('jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
            }
        }

        // Filter by status (status_rujukan)
        if (!empty($filters['status_rujukan'])) {
            if (is_array($filters['status_rujukan'])) {
                $query->whereIn('status_rujukan', $filters['status_rujukan']);
            } else {
                $query->where('status_rujukan', $filters['status_rujukan']);
            }
        }

        // Filter by date range (tanggal_pemeriksaan)
        if (!empty($filters['tanggal_mulai'])) {
            $query->where('tanggal_pemeriksaan', '>=', $filters['tanggal_mulai']);
        }
        if (!empty($filters['tanggal_selesai'])) {
            $query->where('tanggal_pemeriksaan', '<=', $filters['tanggal_selesai']);
        }

        // Search by student name, NIK, or NISN
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->whereHas('studentClassHistory.student', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', $search)
                  ->orWhere('nik', 'like', $search)
                  ->orWhere('nisn', 'like', $search);
            });
        }

        return $query;
    }

    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Referral::query();
        $this->applyFilters($query, $filters);

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        // Custom sorting for related fields if needed, otherwise default sorting
        if (in_array($sortBy, ['nama_lengkap', 'nama_sekolah', 'nama_kelas'])) {
            // We can join tables to sort properly
            $query->join('student_class_histories', 'referrals.student_class_history_id', '=', 'student_class_histories.id')
                  ->join('students', 'student_class_histories.student_id', '=', 'students.id')
                  ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
                  ->join('school_classes', 'student_class_histories.school_class_id', '=', 'school_classes.id')
                  ->select('referrals.*');
            
            if ($sortBy === 'nama_lengkap') {
                $query->orderBy('students.nama_lengkap', $sortOrder);
            } elseif ($sortBy === 'nama_sekolah') {
                $query->orderBy('schools.nama_sekolah', $sortOrder);
            } elseif ($sortBy === 'nama_kelas') {
                $query->orderBy('school_classes.nama_kelas', $sortOrder);
            }
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->with([
            'studentClassHistory.student',
            'studentClassHistory.school',
            'studentClassHistory.schoolClass',
            'pemeriksaan'
        ])->paginate($perPage);
    }

    public function getList(array $filters): Collection
    {
        $query = Referral::query();
        $this->applyFilters($query, $filters);

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        return $query->orderBy($sortBy, $sortOrder)
            ->with([
                'studentClassHistory.student',
                'studentClassHistory.school',
                'studentClassHistory.schoolClass',
                'pemeriksaan'
            ])->get();
    }

    public function findById(int $id): ?Referral
    {
        return Referral::with([
            'studentClassHistory.student',
            'studentClassHistory.school',
            'studentClassHistory.schoolClass',
            'pemeriksaan',
            'statusHistories.user'
        ])->find($id);
    }

    public function save(Referral $referral): Referral
    {
        $referral->save();
        return $referral;
    }

    protected function applyRawFilters($query, array $filters)
    {
        if (!empty($filters['school_id'])) {
            $query->where('student_class_histories.school_id', $filters['school_id']);
        }

        if (!empty($filters['school_class_id'])) {
            $query->where('student_class_histories.school_class_id', $filters['school_class_id']);
        }

        if (!empty($filters['kecamatan_id'])) {
            $query->where('schools.kecamatan_id', $filters['kecamatan_id']);
        }

        if (!empty($filters['kelurahan_id'])) {
            $query->where('schools.kelurahan_id', $filters['kelurahan_id']);
        }

        if (!empty($filters['instansi_id'])) {
            $query->where('schools.instansi_id', $filters['instansi_id']);
        }

        if (!empty($filters['jenis_pemeriksaan'])) {
            if (is_array($filters['jenis_pemeriksaan'])) {
                $query->whereIn('referrals.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
            } else {
                $query->where('referrals.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
            }
        }

        if (!empty($filters['status_rujukan'])) {
            if (is_array($filters['status_rujukan'])) {
                $query->whereIn('referrals.status_rujukan', $filters['status_rujukan']);
            } else {
                $query->where('referrals.status_rujukan', $filters['status_rujukan']);
            }
        }

        if (!empty($filters['tanggal_mulai'])) {
            $query->where('referrals.tanggal_pemeriksaan', '>=', $filters['tanggal_mulai']);
        }

        if (!empty($filters['tanggal_selesai'])) {
            $query->where('referrals.tanggal_pemeriksaan', '<=', $filters['tanggal_selesai']);
        }

        if (!empty($filters['jenjang'])) {
            $jenjang = strtolower($filters['jenjang']);
            if ($jenjang === 'sd') {
                $query->where(function ($sub) {
                    $sub->where('schools.nama_sekolah', 'like', '%SD%')
                        ->orWhere('schools.nama_sekolah', 'like', '%MI%');
                });
            } elseif ($jenjang === 'smp') {
                $query->where(function ($sub) {
                    $sub->where('schools.nama_sekolah', 'like', '%SMP%')
                        ->orWhere('schools.nama_sekolah', 'like', '%MTs%');
                });
            } elseif ($jenjang === 'sma') {
                $query->where(function ($sub) {
                    $sub->where('schools.nama_sekolah', 'like', '%SMA%')
                        ->orWhere('schools.nama_sekolah', 'like', '%SMK%')
                        ->orWhere('schools.nama_sekolah', 'like', '%MA%');
                });
            }
        }

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->join('students', 'student_class_histories.student_id', '=', 'students.id')
                ->where(function ($q) use ($search) {
                    $q->where('students.nama_lengkap', 'like', $search)
                        ->orWhere('students.nik', 'like', $search)
                        ->orWhere('students.nisn', 'like', $search);
                });
        }
    }

    public function getRecapBySchool(array $filters): Collection
    {
        $query = DB::table('referrals')
            ->join('student_class_histories', 'referrals.student_class_history_id', '=', 'student_class_histories.id')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
            ->select(
                'schools.id as school_id',
                'schools.nama_sekolah',
                DB::raw("COUNT(referrals.id) as total_rujukan"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN referrals.status_rujukan = 'Belum Dirujuk' THEN 1 ELSE 0 END), 0) AS UNSIGNED) as belum_dirujuk"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN referrals.status_rujukan = 'Sudah Dirujuk' THEN 1 ELSE 0 END), 0) AS UNSIGNED) as sudah_dirujuk"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN referrals.status_rujukan = 'Dalam Tindak Lanjut' THEN 1 ELSE 0 END), 0) AS UNSIGNED) as dalam_tindak_lanjut"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN referrals.status_rujukan = 'Selesai' THEN 1 ELSE 0 END), 0) AS UNSIGNED) as selesai")
            );

        $this->applyRawFilters($query, $filters);

        return $query->groupBy('schools.id', 'schools.nama_sekolah')
            ->orderBy('schools.nama_sekolah', 'asc')
            ->get();
    }

    public function getRecapByClass(array $filters): Collection
    {
        $query = DB::table('referrals')
            ->join('student_class_histories', 'referrals.student_class_history_id', '=', 'student_class_histories.id')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
            ->join('school_classes', 'student_class_histories.school_class_id', '=', 'school_classes.id')
            ->select(
                'schools.id as school_id',
                'schools.nama_sekolah',
                'school_classes.id as school_class_id',
                'school_classes.nama_kelas',
                DB::raw("COUNT(referrals.id) as total_rujukan"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN referrals.status_rujukan = 'Belum Dirujuk' THEN 1 ELSE 0 END), 0) AS UNSIGNED) as belum_dirujuk"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN referrals.status_rujukan = 'Sudah Dirujuk' THEN 1 ELSE 0 END), 0) AS UNSIGNED) as sudah_dirujuk"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN referrals.status_rujukan = 'Dalam Tindak Lanjut' THEN 1 ELSE 0 END), 0) AS UNSIGNED) as dalam_tindak_lanjut"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN referrals.status_rujukan = 'Selesai' THEN 1 ELSE 0 END), 0) AS UNSIGNED) as selesai")
            );

        $this->applyRawFilters($query, $filters);

        return $query->groupBy('schools.id', 'schools.nama_sekolah', 'school_classes.id', 'school_classes.nama_kelas')
            ->orderBy('schools.nama_sekolah', 'asc')
            ->orderBy('school_classes.nama_kelas', 'asc')
            ->get();
    }

    public function getDashboardStats(array $filters): array
    {
        $baseQuery = DB::table('referrals')
            ->join('student_class_histories', 'referrals.student_class_history_id', '=', 'student_class_histories.id')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id');

        $this->applyRawFilters($baseQuery, $filters);

        // Overall totals by status
        $statusCounts = (clone $baseQuery)
            ->select('referrals.status_rujukan', DB::raw('count(*) as count'))
            ->groupBy('referrals.status_rujukan')
            ->pluck('count', 'referrals.status_rujukan')
            ->toArray();

        // Totals by jenis_pemeriksaan (Gizi, Gigi, Mata, Telinga, Umum)
        $typeCounts = (clone $baseQuery)
            ->select('referrals.jenis_pemeriksaan', DB::raw('count(*) as count'))
            ->groupBy('referrals.jenis_pemeriksaan')
            ->pluck('count', 'referrals.jenis_pemeriksaan')
            ->toArray();

        // Trend over time (monthly, based on tanggal_pemeriksaan)
        $trend = (clone $baseQuery)
            ->select(
                DB::raw("DATE_FORMAT(referrals.tanggal_pemeriksaan, '%Y-%m') as month"),
                DB::raw("count(*) as total")
            )
            ->groupBy(DB::raw("DATE_FORMAT(referrals.tanggal_pemeriksaan, '%Y-%m')"))
            ->orderBy('month', 'asc')
            ->get()
            ->toArray();

        return [
            'status_counts' => [
                'belum_dirujuk' => $statusCounts['Belum Dirujuk'] ?? 0,
                'sudah_dirujuk' => $statusCounts['Sudah Dirujuk'] ?? 0,
                'dalam_tindak_lanjut' => $statusCounts['Dalam Tindak Lanjut'] ?? 0,
                'selesai' => $statusCounts['Selesai'] ?? 0,
                'total' => array_sum($statusCounts),
            ],
            'type_counts' => [
                'gizi' => $typeCounts['Gizi'] ?? 0,
                'gigi' => $typeCounts['Gigi'] ?? 0,
                'mata' => $typeCounts['Mata'] ?? 0,
                'telinga' => $typeCounts['Telinga'] ?? 0,
                'umum' => $typeCounts['Umum'] ?? 0,
            ],
            'trend' => $trend,
        ];
    }
}
