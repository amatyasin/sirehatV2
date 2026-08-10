<?php

namespace App\Repositories\Referral;

use App\Models\Referral;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ReferralRepositoryInterface
{
    /**
     * Get paginated referrals with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get list of referrals with filters (for export or reports).
     *
     * @param array $filters
     * @return Collection
     */
    public function getList(array $filters): Collection;

    /**
     * Find a referral by ID with related relationships.
     *
     * @param int $id
     * @return Referral|null
     */
    public function findById(int $id): ?Referral;

    /**
     * Save/Create/Update a referral.
     *
     * @param Referral $referral
     * @return Referral
     */
    public function save(Referral $referral): Referral;

    /**
     * Get aggregates for school-level reports.
     *
     * @param array $filters
     * @return Collection
     */
    public function getRecapBySchool(array $filters): Collection;

    /**
     * Get aggregates for class-level reports.
     *
     * @param array $filters
     * @return Collection
     */
    public function getRecapByClass(array $filters): Collection;

    /**
     * Get dashboard-level statistics.
     *
     * @param array $filters
     * @return array
     */
    public function getDashboardStats(array $filters): array;
}
