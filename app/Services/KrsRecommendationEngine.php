<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Nilai;
use App\DTOs\KrsSuggestion;
use Illuminate\Support\Collection;

/**
 * KRS Recommendation Engine
 * 
 * Provides intelligent course recommendations based on:
 * - Remaining required courses
 * - Failed courses needing retake
 * - Schedule constraints
 * - SKS allowance
 * - Prerequisites
 */
class KrsRecommendationEngine
{
    protected AkademikCalculationService $calculationService;
    protected AcademicCalendarService $calendarService;

    public function __construct(
        AkademikCalculationService $calculationService,
        AcademicCalendarService $calendarService
    ) {
        $this->calculationService = $calculationService;
        $this->calendarService = $calendarService;
    }

    /**
     * Generate KRS suggestions for a student
     */
    public function suggest(Mahasiswa $mahasiswa): KrsSuggestion
    {
        $context = $this->buildContext($mahasiswa);

        $priorityCourses = $this->generatePriorityCourses($context);
        $optionalCourses = $this->generateOptionalCourses($context, $priorityCourses);
        $warnings = $this->detectWarnings($context);

        return new KrsSuggestion(
            priorityCourses: $priorityCourses,
            optionalCourses: $optionalCourses,
            warnings: $warnings,
            remainingSks: $context['remaining_sks'],
            maxSks: $context['max_sks']
        );
    }

    /**
     * Build context for recommendation
     */
    protected function buildContext(Mahasiswa $mahasiswa): array
    {
        // Get IPS history for max SKS calculation
        $ipsHistory = $this->calculationService->getIPSHistory($mahasiswa);
        $lastIps = $ipsHistory->filter(fn($s) => $s['ips'] > 0)->last()['ips'] ?? 3.0;
        $maxSks = $this->calculationService->getMaxSKS($lastIps);

        // Get current KRS if exists
        $currentKrs = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('tahunAkademik', fn($q) => $q->where('is_active', true))
            ->with('krsDetail.kelas.mataKuliah')
            ->first();

        $currentSks = $currentKrs?->krsDetail->sum(fn($d) => $d->kelas->mataKuliah->sks ?? 0) ?? 0;

        // Get passed courses
        $passedCourseIds = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->whereNotIn('nilai_huruf', ['D', 'E'])
            ->with('kelas.mataKuliah')
            ->get()
            ->pluck('kelas.mataKuliah.id')
            ->filter()
            ->unique()
            ->toArray();

        // Get failed courses (need retake)
        $failedCourses = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('nilai_huruf', ['D', 'E'])
            ->with('kelas.mataKuliah')
            ->get()
            ->pluck('kelas.mataKuliah')
            ->filter()
            ->unique('id');

        // Get already taken in current KRS
        $currentKrsIds = $currentKrs?->krsDetail->pluck('kelas.mata_kuliah_id')->filter()->toArray() ?? [];

        // Calculate current semester
        $currentSemester = $this->calendarService->calculateStudentSemester((int) $mahasiswa->angkatan);

        return [
            'mahasiswa' => $mahasiswa,
            'max_sks' => $maxSks,
            'current_sks' => $currentSks,
            'remaining_sks' => $maxSks - $currentSks,
            'passed_course_ids' => $passedCourseIds,
            'failed_courses' => $failedCourses,
            'current_krs_ids' => $currentKrsIds,
            'current_semester' => $currentSemester,
            'last_ips' => $lastIps,
        ];
    }

    /**
     * Generate priority courses (required courses and retakes)
     */
    protected function generatePriorityCourses(array $context): Collection
    {
        $priorities = collect();

        // Priority 1: Failed courses that need retake
        foreach ($context['failed_courses'] as $failedMk) {
            // Find available class for this course
            $availableClass = Kelas::where('mata_kuliah_id', $failedMk->id)
                ->whereDoesntHave('krsDetail', function ($q) use ($context) {
                    $q->whereHas('krs', fn($q2) => $q2->where('mahasiswa_id', $context['mahasiswa']->id));
                })
                ->with(['mataKuliah', 'dosen.user', 'jadwal'])
                ->withCount('krsDetail')
                ->having('krs_detail_count', '<', \DB::raw('kapasitas'))
                ->first();

            if ($availableClass && !in_array($failedMk->id, $context['current_krs_ids'])) {
                $priorities->push([
                    'kelas' => $availableClass,
                    'reason' => 'Mengulang (nilai sebelumnya: D/E)',
                    'priority' => 1,
                    'sks' => $availableClass->mataKuliah->sks,
                ]);
            }
        }

        // Priority 2: Required courses for current semester
        // Note: Assumes all courses are required if no is_wajib field
        $requiredClasses = Kelas::whereHas('mataKuliah', function ($q) use ($context) {
            $q->where('semester', '<=', $context['current_semester']);
        })
            ->whereNotIn('mata_kuliah_id', $context['passed_course_ids'])
            ->whereNotIn('mata_kuliah_id', $context['current_krs_ids'])
            ->with(['mataKuliah', 'dosen.user', 'jadwal'])
            ->withCount('krsDetail')
            ->get()
            ->filter(fn($k) => $k->krs_detail_count < $k->kapasitas);

        foreach ($requiredClasses as $kelas) {
            $priorities->push([
                'kelas' => $kelas,
                'reason' => 'Mata kuliah wajib semester ' . $kelas->mataKuliah->semester,
                'priority' => 2,
                'sks' => $kelas->mataKuliah->sks,
            ]);
        }

        return $priorities->sortBy('priority')->take(10);
    }

    /**
     * Generate optional/elective courses
     */
    protected function generateOptionalCourses(array $context, Collection $priorities): Collection
    {
        $usedSks = $priorities->sum('sks');
        $remainingAfterPriority = $context['remaining_sks'] - $usedSks;

        if ($remainingAfterPriority <= 0) {
            return collect();
        }

        $priorityMkIds = $priorities->pluck('kelas.mata_kuliah_id')->toArray();

        // Get additional courses (beyond current semester)
        $electives = Kelas::whereHas('mataKuliah', function ($q) use ($context, $remainingAfterPriority) {
            $q->where('semester', '>', $context['current_semester'])
                ->where('sks', '<=', $remainingAfterPriority);
        })
            ->whereNotIn('mata_kuliah_id', $context['passed_course_ids'])
            ->whereNotIn('mata_kuliah_id', $context['current_krs_ids'])
            ->whereNotIn('mata_kuliah_id', $priorityMkIds)
            ->with(['mataKuliah', 'dosen.user', 'jadwal'])
            ->withCount('krsDetail')
            ->get()
            ->filter(fn($k) => $k->krs_detail_count < $k->kapasitas);

        return $electives->map(fn($kelas) => [
            'kelas' => $kelas,
            'reason' => 'Mata kuliah pilihan',
            'priority' => 3,
            'sks' => $kelas->mataKuliah->sks,
        ])->take(5);
    }

    /**
     * Detect potential warnings for student
     */
    protected function detectWarnings(array $context): array
    {
        $warnings = [];

        // Check if IPS is low
        if ($context['last_ips'] < 2.5) {
            $warnings[] = [
                'type' => 'LOW_IPS',
                'message' => "IPS terakhir ({$context['last_ips']}) di bawah rata-rata. Pertimbangkan mengurangi beban SKS.",
            ];
        }

        // Check if many failed courses
        if ($context['failed_courses']->count() >= 3) {
            $warnings[] = [
                'type' => 'MANY_RETAKES',
                'message' => "Ada {$context['failed_courses']->count()} mata kuliah yang perlu diulang.",
            ];
        }

        // Check if behind schedule
        $expectedSemester = $context['current_semester'];
        $passedMkCount = count($context['passed_course_ids']);
        $expectedPassed = $expectedSemester * 5; // ~5 courses per semester

        if ($passedMkCount < $expectedPassed * 0.7) {
            $warnings[] = [
                'type' => 'BEHIND_SCHEDULE',
                'message' => "Progress studi di bawah target. Pertimbangkan konsultasi dengan Dosen PA.",
            ];
        }

        return $warnings;
    }
}
