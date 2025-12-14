<?php

namespace App\Services;

use App\Models\TahunAkademik;
use Carbon\Carbon;

/**
 * Academic Calendar Service
 * 
 * Centralizes all academic period logic to avoid scattered TahunAkademik queries.
 * Provides temporal boundaries for enrollment, grading, and academic operations.
 */
class AcademicCalendarService
{
    protected ?TahunAkademik $currentPeriod = null;

    /**
     * Get the current active academic period
     */
    public function currentPeriod(): ?TahunAkademik
    {
        if ($this->currentPeriod === null) {
            $this->currentPeriod = TahunAkademik::where('is_active', true)->first();
        }

        return $this->currentPeriod;
    }

    /**
     * Get current period ID (cached)
     */
    public function currentPeriodId(): ?int
    {
        return $this->currentPeriod()?->id;
    }

    /**
     * Get formatted period name (e.g., "2024/2025 Ganjil")
     */
    public function currentPeriodName(): string
    {
        $period = $this->currentPeriod();
        if (!$period) {
            return 'Tidak ada periode aktif';
        }

        $semesterLabel = config('siakad.semester_labels')[$period->semester] ?? $period->semester;
        return "{$period->tahun} {$semesterLabel}";
    }

    /**
     * Check if we're in enrollment period
     * Default: enrollment is open if no specific dates are set
     */
    public function isEnrollmentOpen(): bool
    {
        $period = $this->currentPeriod();
        if (!$period) {
            return false;
        }

        // If period has enrollment dates, check them
        if ($period->enrollment_start && $period->enrollment_end) {
            $now = Carbon::now();
            return $now->between(
                Carbon::parse($period->enrollment_start),
                Carbon::parse($period->enrollment_end)
            );
        }

        // Default: enrollment is open if period is active
        return true;
    }

    /**
     * Check if we're in late enrollment period (add/drop)
     */
    public function isLateEnrollmentOpen(): bool
    {
        $period = $this->currentPeriod();
        if (!$period) {
            return false;
        }

        if ($period->late_enrollment_end) {
            return Carbon::now()->lte(Carbon::parse($period->late_enrollment_end));
        }

        // Default: allow late enrollment for 2 weeks after enrollment ends
        if ($period->enrollment_end) {
            return Carbon::now()->lte(
                Carbon::parse($period->enrollment_end)->addWeeks(2)
            );
        }

        return $this->isEnrollmentOpen();
    }

    /**
     * Check if grading period is open
     */
    public function isGradingOpen(): bool
    {
        $period = $this->currentPeriod();
        if (!$period) {
            return false;
        }

        if ($period->grading_start && $period->grading_end) {
            $now = Carbon::now();
            return $now->between(
                Carbon::parse($period->grading_start),
                Carbon::parse($period->grading_end)
            );
        }

        // Default: grading is always open during active period
        return true;
    }

    /**
     * Get current semester number (1 = Ganjil, 2 = Genap)
     */
    public function currentSemesterNumber(): int
    {
        return $this->currentPeriod()?->semester ?? 1;
    }

    /**
     * Get current academic year (e.g., "2024/2025")
     */
    public function currentYear(): string
    {
        return $this->currentPeriod()?->tahun ?? date('Y') . '/' . (date('Y') + 1);
    }

    /**
     * Calculate student's active semester based on enrollment year
     */
    public function calculateStudentSemester(int $enrollmentYear): int
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $yearsEnrolled = $currentYear - $enrollmentYear;
        $semester = $yearsEnrolled * 2;

        // If August or later, we're in odd semester
        if ($currentMonth >= 8) {
            $semester += 1;
        }

        return max(1, $semester);
    }

    /**
     * Get phase description for current period
     */
    public function getCurrentPhase(): string
    {
        if ($this->isEnrollmentOpen()) {
            return 'Masa Pengisian KRS';
        }

        if ($this->isLateEnrollmentOpen()) {
            return 'Masa Perubahan KRS';
        }

        if ($this->isGradingOpen()) {
            return 'Masa Input Nilai';
        }

        return 'Perkuliahan Aktif';
    }

    /**
     * Get days until enrollment closes
     */
    public function daysUntilEnrollmentCloses(): ?int
    {
        $period = $this->currentPeriod();
        if (!$period || !$period->enrollment_end) {
            return null;
        }

        $end = Carbon::parse($period->enrollment_end);
        $now = Carbon::now();

        if ($now->gt($end)) {
            return 0;
        }

        return $now->diffInDays($end);
    }

    /**
     * Clear cached period (useful for testing or after period change)
     */
    public function refresh(): self
    {
        $this->currentPeriod = null;
        return $this;
    }
}
