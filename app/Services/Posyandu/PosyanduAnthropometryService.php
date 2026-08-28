<?php

namespace App\Services\Posyandu;

use Carbon\Carbon;

class PosyanduAnthropometryService
{
    /**
     * Calculate age in completed months between birth date and examination date.
     */
    public function calculateAgeMonths(?string $birthDate, ?string $examDate = null): int
    {
        if (! $birthDate) {
            return 0;
        }

        $born = Carbon::parse($birthDate);
        $exam = $examDate ? Carbon::parse($examDate) : now();

        return (int) floor($born->diffInMonths($exam));
    }

    /**
     * Calculate Body Mass Index (BMI = Weight / (Height_m)^2).
     */
    public function calculateBMI(float $weightKg, float $heightCm): float
    {
        if ($heightCm <= 0 || $weightKg <= 0) {
            return 0.0;
        }

        $heightM = $heightCm / 100.0;

        return round($weightKg / ($heightM * $heightM), 2);
    }

    /**
     * Determine BMI Category based on standard WHO thresholds for children.
     */
    public function determineBMICategory(float $bmi): string
    {
        if ($bmi <= 0) {
            return '-';
        }

        if ($bmi < 14.0) {
            return 'Kurus';
        }

        if ($bmi <= 17.0) {
            return 'Normal';
        }

        if ($bmi <= 19.0) {
            return 'Gemuk';
        }

        return 'Obesitas';
    }

    /**
     * Determine Stunting status (TB/U) based on age in months and height in cm.
     */
    public function determineStuntingStatus(int $ageMonths, float $heightCm): string
    {
        if ($heightCm <= 0) {
            return '-';
        }

        if ($ageMonths <= 12) {
            if ($heightCm < 70.0) {
                return 'Sangat Pendek';
            }
            if ($heightCm < 74.0) {
                return 'Pendek';
            }

            return 'Normal';
        }

        if ($ageMonths <= 24) {
            if ($heightCm < 78.0) {
                return 'Sangat Pendek';
            }
            if ($heightCm < 82.0) {
                return 'Pendek';
            }

            return 'Normal';
        }

        if ($ageMonths <= 36) {
            if ($heightCm < 86.0) {
                return 'Sangat Pendek';
            }
            if ($heightCm < 90.0) {
                return 'Pendek';
            }

            return 'Normal';
        }

        if ($heightCm < 95.0) {
            return 'Sangat Pendek';
        }
        if ($heightCm < 100.0) {
            return 'Pendek';
        }

        return 'Normal';
    }

    /**
     * Determine Head Circumference status (LK/U).
     */
    public function determineHeadCircumferenceResult(float $headCircumferenceCm): string
    {
        if ($headCircumferenceCm <= 0) {
            return '-';
        }

        if ($headCircumferenceCm < 42.0) {
            return 'Mikrosefali';
        }

        if ($headCircumferenceCm > 52.0) {
            return 'Makrosefali';
        }

        return 'Normal';
    }

    /**
     * Determine TB Screening result.
     */
    public function determineTBScreening(string $cough, string $fever, string $weightProb, string $contact): string
    {
        $coughY = strtoupper(trim($cough)) === 'Y';
        $feverY = strtoupper(trim($fever)) === 'Y';
        $weightY = strtoupper(trim($weightProb)) === 'Y';
        $contactY = strtoupper(trim($contact)) === 'Y';

        if ($coughY || $feverY || $weightY || $contactY) {
            return 'Terindikasi';
        }

        return 'Tidak Terindikasi';
    }

    /**
     * Determine overall examination status.
     */
    public function determineExaminationStatus(
        bool $attended,
        ?float $weight,
        ?float $height,
        string $stuntingStatus,
        string $tbResult
    ): string {
        if (! $attended) {
            return 'Belum Diperiksa';
        }

        if (empty($weight) || empty($height)) {
            return 'Belum Diperiksa';
        }

        if (in_array($stuntingStatus, ['Pendek', 'Sangat Pendek']) || $tbResult === 'Terindikasi') {
            return 'Perlu Tindak Lanjut';
        }

        return 'Sudah Diperiksa';
    }
}
