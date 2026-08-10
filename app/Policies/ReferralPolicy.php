<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Referral;

class ReferralPolicy
{
    /**
     * Determine if the user can view any referrals.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() ||
            $user->isAdminDinkes() ||
            $user->hasRole('admin_kecamatan') ||
            $user->isAdminInstansi() ||
            $user->hasRole('admin_sekolah');
    }

    /**
     * Determine if the user can view a specific referral.
     */
    public function view(User $user, Referral $referral): bool
    {
        if ($user->isSuperAdmin() || $user->isAdminDinkes()) {
            return true;
        }

        $referralSchool = $referral->studentClassHistory?->school;
        if (!$referralSchool) {
            return false;
        }

        if ($user->hasRole('admin_kecamatan')) {
            return $user->kecamatan_id === $referralSchool->kecamatan_id;
        }

        if ($user->isAdminInstansi()) {
            return $user->instansi_id === $referralSchool->instansi_id;
        }

        if ($user->hasRole('admin_sekolah')) {
            return $user->school_id === $referralSchool->id;
        }

        return false;
    }

    /**
     * Determine if the user can update a specific referral.
     */
    public function update(User $user, Referral $referral): bool
    {
        return $this->view($user, $referral);
    }
}
