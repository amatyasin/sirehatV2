<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReferralStatusRequest;
use App\Http\Requests\ExportReferralsRequest;
use App\Http\Resources\ReferralResource;
use App\Services\Referral\ReferralService;
use App\Models\User;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Referral;
use App\Exports\ReferralsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ReferralApiController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Helper to restrict query scope based on user role.
     */
    private function applyRoleRestrictions(User $user, array &$filters)
    {
        if ($user->isSuperAdmin() || $user->isAdminDinkes()) {
            return;
        }

        if ($user->hasRole('admin_kecamatan')) {
            $filters['kecamatan_id'] = $user->kecamatan_id;
        } elseif ($user->isAdminInstansi()) {
            $filters['instansi_id'] = $user->instansi_id;
        } elseif ($user->hasRole('admin_sekolah')) {
            $filters['school_id'] = $user->school_id;
        } else {
            // No permissions, restrict to invalid key
            $filters['school_id'] = -1;
        }
    }

    /**
     * Get list of referrals (paginated).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $filters = $request->all();
        $this->applyRoleRestrictions($user, $filters);

        $perPage = (int) $request->query('per_page', 15);
        $referrals = $this->referralService->getPaginatedReferrals($filters, $perPage);

        return ReferralResource::collection($referrals);
    }

    /**
     * Get specific referral details.
     */
    public function show(int $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $referral = $this->referralService->getReferralDetail($id);
        if (!$referral) {
            return response()->json(['message' => 'Referral not found'], 404);
        }

        // Authorize access
        if ($user->isSuperAdmin() || $user->isAdminDinkes()) {
            // super admin can access
        } else {
            $school = $referral->studentClassHistory?->school;
            if (!$school) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($user->hasRole('admin_kecamatan') && $user->kecamatan_id !== $school->kecamatan_id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($user->isAdminInstansi() && $user->instansi_id !== $school->instansi_id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($user->hasRole('admin_sekolah') && $user->school_id !== $school->id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        return new ReferralResource($referral);
    }

    /**
     * Update referral status.
     */
    public function updateStatus(UpdateReferralStatusRequest $request, int $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $referral = Referral::find($id);
        if (!$referral) {
            return response()->json(['message' => 'Referral not found'], 404);
        }

        // Authorize access
        if ($user->isSuperAdmin() || $user->isAdminDinkes()) {
            // allowed
        } else {
            $school = $referral->studentClassHistory?->school;
            if (!$school) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($user->hasRole('admin_kecamatan') && $user->kecamatan_id !== $school->kecamatan_id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($user->isAdminInstansi() && $user->instansi_id !== $school->instansi_id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($user->hasRole('admin_sekolah') && $user->school_id !== $school->id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $updatedReferral = $this->referralService->updateStatus(
            $id,
            $request->input('status_rujukan'),
            $request->input('catatan'),
            $user->id
        );

        return new ReferralResource($updatedReferral);
    }

    /**
     * Get school-level referral summaries.
     */
    public function recapSchool(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $filters = $request->all();
        $this->applyRoleRestrictions($user, $filters);

        $recap = $this->referralService->getSchoolRecap($filters);
        return response()->json($recap);
    }

    /**
     * Get class-level referral summaries.
     */
    public function recapClass(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $filters = $request->all();
        $this->applyRoleRestrictions($user, $filters);

        $recap = $this->referralService->getClassRecap($filters);
        return response()->json($recap);
    }

    /**
     * Get dashboard analytical stats.
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $filters = $request->all();
        $this->applyRoleRestrictions($user, $filters);

        $stats = $this->referralService->getStats($filters);
        return response()->json($stats);
    }

    /**
     * Export data.
     */
    public function export(ExportReferralsRequest $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $filters = $request->all();
        $this->applyRoleRestrictions($user, $filters);

        $format = $request->input('format', 'xlsx');
        $referrals = $this->referralService->getReferralsList($filters);

        if ($format === 'pdf') {
            $schoolName = 'Semua Sekolah';
            if (!empty($filters['school_id'])) {
                $schoolName = School::find($filters['school_id'])?->nama_sekolah ?? 'Semua Sekolah';
            }
            $className = 'Semua Kelas';
            if (!empty($filters['school_class_id'])) {
                $className = SchoolClass::find($filters['school_class_id'])?->nama_kelas ?? 'Semua Kelas';
            }
            $pemeriksaanType = $filters['jenis_pemeriksaan'] ?? 'Semua Jenis';
            $statusName = $filters['status_rujukan'] ?? 'Semua Status';

            $pdf = Pdf::loadView('referrals.export_pdf', [
                'referrals' => $referrals,
                'school_name' => $schoolName,
                'class_name' => $className,
                'pemeriksaan_type' => $pemeriksaanType,
                'status_name' => $statusName,
            ])->setPaper('a4', 'landscape');
            
            return $pdf->download('daftar_rujukan_' . now()->format('Ymd_His') . '.pdf');
        }

        $fileName = 'daftar_rujukan_' . now()->format('Ymd_His') . '.' . $format;
        return Excel::download(new ReferralsExport($referrals), $fileName);
    }

    /**
     * Get options for filter dropdowns.
     */
    public function options()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $schoolsQuery = School::query()->select('id', 'nama_sekolah', 'kecamatan_id', 'instansi_id');
        $classesQuery = SchoolClass::query()->select('id', 'school_id', 'nama_kelas');
        $kecamatanQuery = \App\Models\Kecamatan::query()->select('id', 'nama_kecamatan');
        $kelurahanQuery = \App\Models\Kelurahan::query()->select('id', 'kecamatan_id', 'nama_kelurahan');

        if (!$user->isSuperAdmin() && !$user->isAdminDinkes()) {
            if ($user->hasRole('admin_kecamatan')) {
                $schoolsQuery->where('kecamatan_id', $user->kecamatan_id);
                $kecamatanQuery->where('id', $user->kecamatan_id);
                $kelurahanQuery->where('kecamatan_id', $user->kecamatan_id);
            } elseif ($user->isAdminInstansi()) {
                $schoolsQuery->where('instansi_id', $user->instansi_id);
            } elseif ($user->hasRole('admin_sekolah')) {
                $schoolsQuery->where('id', $user->school_id);
            }
        }

        $schools = $schoolsQuery->get();
        $schoolIds = $schools->pluck('id')->toArray();

        if (!$user->isSuperAdmin() && !$user->isAdminDinkes()) {
            $classesQuery->whereIn('school_id', $schoolIds);
        }
        $classes = $classesQuery->get();

        return response()->json([
            'schools' => $schools,
            'classes' => $classes,
            'kecamatans' => $kecamatanQuery->get(),
            'kelurahans' => $kelurahanQuery->get(),
            'user' => [
                'name' => $user->name,
                'role' => $user->roles->first()?->name ?? 'Guest',
                'school_id' => $user->school_id,
                'kecamatan_id' => $user->kecamatan_id,
                'instansi_id' => $user->instansi_id,
            ],
            'status_options' => ['Belum Dirujuk', 'Sudah Dirujuk', 'Dalam Tindak Lanjut', 'Selesai'],
            'jenis_options' => ['Gizi', 'Gigi', 'Mata', 'Telinga', 'Umum'],
        ]);
    }
}
