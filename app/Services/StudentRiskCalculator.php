<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Krs;
use App\Models\Presensi;
use App\DTOs\RiskProfile;

/**
 * Student Risk Calculator
 * 
 * Computes composite risk scores for students based on:
 * - IPS trend (declining/stable/improving)
 * - Attendance patterns
 * - Course retakes
 * - Graduation progress
 * - Workload balance
 */
class StudentRiskCalculator
{
    protected AkademikCalculationService $calculationService;
    protected PresensiService $presensiService;

    // Configurable weights for risk factors
    protected array $weights = [
        'ips_trend' => 0.25,
        'attendance' => 0.20,
        'retakes' => 0.15,
        'graduation_progress' => 0.25,
        'workload' => 0.15,
    ];

    public function __construct(
        AkademikCalculationService $calculationService,
        PresensiService $presensiService
    ) {
        $this->calculationService = $calculationService;
        $this->presensiService = $presensiService;
    }

    /**
     * Calculate comprehensive risk profile for a student
     */
    public function calculate(Mahasiswa $mahasiswa): RiskProfile
    {
        $factors = [
            'ips_trend' => $this->calculateIpsTrend($mahasiswa),
            'attendance' => $this->calculateAttendanceRisk($mahasiswa),
            'retakes' => $this->calculateRetakeRisk($mahasiswa),
            'graduation_progress' => $this->calculateGraduationRisk($mahasiswa),
            'workload' => $this->calculateWorkloadRisk($mahasiswa),
        ];

        $score = $this->computeWeightedScore($factors);
        $level = $this->scoreToLevel($score);
        $flags = $this->deriveFlags($factors);
        $recommendations = $this->generateRecommendations($flags, $factors);

        return new RiskProfile(
            score: $score,
            level: $level,
            flags: $flags,
            factors: $factors,
            recommendations: $recommendations
        );
    }

    /**
     * Calculate IPS trend risk (-1 = declining, 0 = stable, 1 = improving)
     * Returns risk value (0-1, higher = more risk)
     */
    protected function calculateIpsTrend(Mahasiswa $mahasiswa): float
    {
        $ipsHistory = $this->calculationService->getIPSHistory($mahasiswa);

        if ($ipsHistory->count() < 2) {
            return 0.3; // Neutral risk for new students
        }

        $recentTwo = $ipsHistory->take(-2)->values();
        $previousIps = $recentTwo[0]['ips'] ?? 0;
        $currentIps = $recentTwo[1]['ips'] ?? 0;

        $change = $currentIps - $previousIps;

        if ($change < -0.5) {
            return 0.9; // Steep decline = high risk
        } elseif ($change < -0.2) {
            return 0.7; // Moderate decline
        } elseif ($change < 0.2) {
            return 0.3; // Stable
        } else {
            return 0.1; // Improving
        }
    }

    /**
     * Calculate attendance risk based on presensi data
     */
    protected function calculateAttendanceRisk(Mahasiswa $mahasiswa): float
    {
        // Get active semester classes
        $activeKrs = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'approved')
            ->whereHas('tahunAkademik', fn($q) => $q->where('is_active', true))
            ->with('krsDetail.kelas')
            ->first();

        if (!$activeKrs || $activeKrs->krsDetail->isEmpty()) {
            return 0.3; // No data = neutral
        }

        $totalPercentage = 0;
        $classCount = 0;
        $hasData = false;

        foreach ($activeKrs->krsDetail as $detail) {
            $rekap = $this->presensiService->getRekapPresensi($mahasiswa->id, $detail->kelas_id);

            if ($rekap['total_pertemuan'] > 0) {
                $hasData = true;
                $totalPercentage += $rekap['persentase'];
                $classCount++;
            }
        }

        if (!$hasData || $classCount === 0) {
            return 0.3; // No attendance data
        }

        $avgAttendance = $totalPercentage / $classCount;

        // Convert to risk (lower attendance = higher risk)
        if ($avgAttendance >= 90) {
            return 0.1;
        } elseif ($avgAttendance >= 80) {
            return 0.3;
        } elseif ($avgAttendance >= 70) {
            return 0.5;
        } elseif ($avgAttendance >= 60) {
            return 0.7;
        } else {
            return 0.9;
        }
    }

    /**
     * Calculate retake risk based on failed/repeated courses
     */
    protected function calculateRetakeRisk(Mahasiswa $mahasiswa): float
    {
        $retakeCount = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('nilai_huruf', ['D', 'E'])
            ->count();

        if ($retakeCount === 0) {
            return 0.0;
        } elseif ($retakeCount <= 2) {
            return 0.3;
        } elseif ($retakeCount <= 4) {
            return 0.6;
        } else {
            return 0.9;
        }
    }

    /**
     * Calculate graduation progress risk
     */
    protected function calculateGraduationRisk(Mahasiswa $mahasiswa): float
    {
        $ipkData = $this->calculationService->calculateIPK($mahasiswa);
        $totalSks = $ipkData['total_sks'];
        $targetSks = config('academic_rules.default.graduation_total_sks', 144);

        // Calculate expected progress based on semester
        $currentSemester = $this->calculateCurrentSemester($mahasiswa);
        $expectedSks = min(($currentSemester / 8) * $targetSks, $targetSks);

        if ($totalSks >= $expectedSks) {
            return 0.1; // On track
        }

        $deficit = $expectedSks - $totalSks;
        $deficitRatio = $deficit / $targetSks;

        if ($deficitRatio <= 0.1) {
            return 0.3; // Slightly behind
        } elseif ($deficitRatio <= 0.2) {
            return 0.5; // Moderately behind
        } elseif ($deficitRatio <= 0.3) {
            return 0.7; // Significantly behind
        } else {
            return 0.9; // Severely behind
        }
    }

    /**
     * Calculate workload risk (overloading or underloading)
     */
    protected function calculateWorkloadRisk(Mahasiswa $mahasiswa): float
    {
        $ipsHistory = $this->calculationService->getIPSHistory($mahasiswa);
        $lastIps = $ipsHistory->last()['ips'] ?? 3.0;
        $maxSks = $this->calculationService->getMaxSKS($lastIps);

        $activeKrs = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'approved')
            ->whereHas('tahunAkademik', fn($q) => $q->where('is_active', true))
            ->with('krsDetail.kelas.mataKuliah')
            ->first();

        if (!$activeKrs) {
            return 0.3;
        }

        $currentSks = $activeKrs->krsDetail->sum(fn($d) => $d->kelas->mataKuliah->sks ?? 0);
        $utilizationRatio = $currentSks / max($maxSks, 1);

        if ($utilizationRatio > 1.0) {
            return 0.8; // Overloading
        } elseif ($utilizationRatio >= 0.7) {
            return 0.2; // Good utilization
        } elseif ($utilizationRatio >= 0.5) {
            return 0.4; // Underutilizing
        } else {
            return 0.6; // Severely underloading (may indicate issues)
        }
    }

    /**
     * Calculate current semester for student
     */
    protected function calculateCurrentSemester(Mahasiswa $mahasiswa): int
    {
        $angkatan = (int) $mahasiswa->angkatan;
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $yearsEnrolled = $currentYear - $angkatan;
        $semester = $yearsEnrolled * 2;

        if ($currentMonth >= 8) {
            $semester += 1;
        }

        return max(1, $semester);
    }

    /**
     * Compute weighted score from factors
     */
    protected function computeWeightedScore(array $factors): int
    {
        $score = 0;
        foreach ($factors as $key => $value) {
            $weight = $this->weights[$key] ?? 0;
            $score += $value * $weight * 100;
        }
        return (int) round($score);
    }

    /**
     * Convert score to risk level
     */
    protected function scoreToLevel(int $score): string
    {
        if ($score <= 25) {
            return 'LOW';
        } elseif ($score <= 45) {
            return 'MEDIUM';
        } elseif ($score <= 65) {
            return 'HIGH';
        } else {
            return 'CRITICAL';
        }
    }

    /**
     * Derive risk flags from factors
     */
    protected function deriveFlags(array $factors): array
    {
        $flags = [];

        if ($factors['ips_trend'] >= 0.7) {
            $flags[] = 'DECLINING_PERFORMANCE';
        }

        if ($factors['attendance'] >= 0.7) {
            $flags[] = 'ATTENDANCE_WARNING';
        }

        if ($factors['retakes'] >= 0.6) {
            $flags[] = 'MULTIPLE_RETAKES';
        }

        if ($factors['graduation_progress'] >= 0.7) {
            $flags[] = 'GRADUATION_DELAY_RISK';
        }

        if ($factors['workload'] >= 0.6) {
            $flags[] = 'WORKLOAD_IMBALANCE';
        }

        return $flags;
    }

    /**
     * Generate recommendations based on flags
     */
    protected function generateRecommendations(array $flags, array $factors): array
    {
        $recommendations = [];

        if (in_array('DECLINING_PERFORMANCE', $flags)) {
            $recommendations[] = 'Konsultasi dengan Dosen PA untuk evaluasi beban studi';
        }

        if (in_array('ATTENDANCE_WARNING', $flags)) {
            $recommendations[] = 'Tingkatkan kehadiran kuliah untuk menghindari sanksi akademik';
        }

        if (in_array('MULTIPLE_RETAKES', $flags)) {
            $recommendations[] = 'Fokus mengulang mata kuliah yang belum lulus sebelum mengambil MK baru';
        }

        if (in_array('GRADUATION_DELAY_RISK', $flags)) {
            $recommendations[] = 'Pertimbangkan semester pendek atau tambah SKS untuk mengejar ketertinggalan';
        }

        if (in_array('WORKLOAD_IMBALANCE', $flags)) {
            $recommendations[] = 'Sesuaikan jumlah SKS dengan kemampuan berdasarkan IPS';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Pertahankan performa akademik yang baik';
        }

        return $recommendations;
    }
}
