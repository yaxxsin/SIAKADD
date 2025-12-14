<?php

namespace App\Domain\Enrollment;

use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Nilai;
use App\Models\JadwalKuliah;
use App\Services\AcademicCalendarService;
use App\Services\AkademikCalculationService;
use App\Exceptions\EnrollmentException;

/**
 * Enrollment Policy
 * 
 * Centralizes all enrollment business rules and policy checks.
 * Extracted from KrsService to provide a single source of truth
 * for enrollment validation logic.
 */
class EnrollmentPolicy
{
    public function __construct(
        protected AcademicCalendarService $calendar,
        protected AkademikCalculationService $calculation
    ) {
    }

    /**
     * Validate that enrollment is currently open
     * 
     * @throws EnrollmentException
     */
    public function assertEnrollmentOpen(): void
    {
        if (!$this->calendar->isEnrollmentOpen() && !$this->calendar->isLateEnrollmentOpen()) {
            throw EnrollmentException::enrollmentClosed();
        }
    }

    /**
     * Validate KRS is in editable state
     * 
     * @throws EnrollmentException
     */
    public function assertKrsEditable(Krs $krs): void
    {
        if ($krs->status !== 'draft') {
            throw EnrollmentException::krsLocked($krs->status);
        }
    }

    /**
     * Validate class capacity
     * 
     * @throws EnrollmentException
     */
    public function assertClassHasCapacity(Kelas $kelas): void
    {
        $enrolled = KrsDetail::where('kelas_id', $kelas->id)->count();

        if ($enrolled >= $kelas->kapasitas) {
            throw EnrollmentException::classFull(
                $kelas->mataKuliah->nama_mk ?? 'Unknown',
                $kelas->kapasitas
            );
        }
    }

    /**
     * Validate course is not already taken in current KRS
     * 
     * @throws EnrollmentException
     */
    public function assertCourseNotDuplicate(Krs $krs, Kelas $kelas): void
    {
        $alreadyTaken = $krs->krsDetail()
            ->whereHas('kelas', function ($q) use ($kelas) {
                $q->where('mata_kuliah_id', $kelas->mata_kuliah_id);
            })
            ->exists();

        if ($alreadyTaken) {
            throw EnrollmentException::duplicateCourse(
                $kelas->mataKuliah->nama_mk ?? 'Unknown'
            );
        }
    }

    /**
     * Validate SKS limit
     * 
     * @throws EnrollmentException
     */
    public function assertSksWithinLimit(Mahasiswa $mahasiswa, Krs $krs, int $addingSks): void
    {
        $currentSks = $this->calculateCurrentSks($krs);
        $maxSks = $this->getMaxSks($mahasiswa);

        if (($currentSks + $addingSks) > $maxSks) {
            throw EnrollmentException::sksLimitExceeded($currentSks, $maxSks, $addingSks);
        }
    }

    /**
     * Validate no schedule conflicts
     * 
     * @throws EnrollmentException
     */
    public function assertNoScheduleConflict(Krs $krs, Kelas $newKelas): void
    {
        // Get schedule for new class
        $newSchedules = JadwalKuliah::where('kelas_id', $newKelas->id)->get();

        if ($newSchedules->isEmpty()) {
            return; // No schedule defined, skip check
        }

        // Get existing schedules from KRS
        $existingKelasIds = $krs->krsDetail()->pluck('kelas_id')->toArray();
        $existingSchedules = JadwalKuliah::whereIn('kelas_id', $existingKelasIds)->get();

        foreach ($newSchedules as $newSchedule) {
            foreach ($existingSchedules as $existing) {
                if ($this->schedulesOverlap($newSchedule, $existing)) {
                    $existingKelas = Kelas::with('mataKuliah')->find($existing->kelas_id);

                    throw EnrollmentException::scheduleConflict(
                        $newKelas->mataKuliah->nama_mk ?? 'Unknown',
                        $existingKelas->mataKuliah->nama_mk ?? 'Unknown',
                        "{$newSchedule->hari} {$newSchedule->jam_mulai}"
                    );
                }
            }
        }
    }

    /**
     * Validate prerequisites are met
     * 
     * @throws EnrollmentException
     */
    public function assertPrerequisitesMet(Mahasiswa $mahasiswa, Kelas $kelas): void
    {
        $mataKuliah = $kelas->mataKuliah;

        // Check if MK has prerequisites defined
        if (!$mataKuliah || empty($mataKuliah->prasyarat_id)) {
            return;
        }

        // Check if student has passed the prerequisite
        $hasPassed = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('kelas', function ($q) use ($mataKuliah) {
                $q->where('mata_kuliah_id', $mataKuliah->prasyarat_id);
            })
            ->whereNotIn('nilai_huruf', ['D', 'E'])
            ->exists();

        if (!$hasPassed) {
            $prasyarat = \App\Models\MataKuliah::find($mataKuliah->prasyarat_id);
            throw EnrollmentException::prerequisiteNotMet(
                $mataKuliah->nama_mk,
                $prasyarat->nama_mk ?? 'Unknown'
            );
        }
    }

    /**
     * Run all enrollment validations
     * 
     * @throws EnrollmentException
     */
    public function validateEnrollment(Mahasiswa $mahasiswa, Krs $krs, Kelas $kelas): void
    {
        $this->assertEnrollmentOpen();
        $this->assertKrsEditable($krs);
        $this->assertClassHasCapacity($kelas);
        $this->assertCourseNotDuplicate($krs, $kelas);
        $this->assertSksWithinLimit($mahasiswa, $krs, $kelas->mataKuliah->sks ?? 0);
        $this->assertNoScheduleConflict($krs, $kelas);
        $this->assertPrerequisitesMet($mahasiswa, $kelas);
    }

    /**
     * Validate KRS can be submitted
     * 
     * @throws EnrollmentException
     */
    public function validateSubmission(Krs $krs): void
    {
        if ($krs->krsDetail()->count() === 0) {
            throw EnrollmentException::krsEmpty();
        }
    }

    /**
     * Calculate current SKS in KRS
     */
    public function calculateCurrentSks(Krs $krs): int
    {
        return $krs->krsDetail()
            ->with('kelas.mataKuliah')
            ->get()
            ->sum(fn($detail) => $detail->kelas->mataKuliah->sks ?? 0);
    }

    /**
     * Get maximum SKS allowed for student
     */
    public function getMaxSks(Mahasiswa $mahasiswa): int
    {
        $ipsHistory = $this->calculation->getIPSHistory($mahasiswa);
        $lastIps = $ipsHistory->filter(fn($s) => $s['ips'] > 0)->last()['ips'] ?? 3.0;

        return $this->calculation->getMaxSKS($lastIps);
    }

    /**
     * Check if two schedules overlap
     */
    protected function schedulesOverlap(JadwalKuliah $a, JadwalKuliah $b): bool
    {
        // Must be same day
        if ($a->hari !== $b->hari) {
            return false;
        }

        // Convert to comparable format
        $aStart = strtotime($a->jam_mulai);
        $aEnd = strtotime($a->jam_selesai);
        $bStart = strtotime($b->jam_mulai);
        $bEnd = strtotime($b->jam_selesai);

        // Check overlap
        return ($aStart < $bEnd) && ($aEnd > $bStart);
    }

    /**
     * Get enrollment status summary
     */
    public function getEnrollmentStatus(Mahasiswa $mahasiswa, Krs $krs): array
    {
        $currentSks = $this->calculateCurrentSks($krs);
        $maxSks = $this->getMaxSks($mahasiswa);

        return [
            'krs_status' => $krs->status,
            'current_sks' => $currentSks,
            'max_sks' => $maxSks,
            'remaining_sks' => $maxSks - $currentSks,
            'can_add_more' => $currentSks < $maxSks,
            'enrollment_open' => $this->calendar->isEnrollmentOpen(),
            'late_enrollment_open' => $this->calendar->isLateEnrollmentOpen(),
            'days_until_close' => $this->calendar->daysUntilEnrollmentCloses(),
        ];
    }
}
