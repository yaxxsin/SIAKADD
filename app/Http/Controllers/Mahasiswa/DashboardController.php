<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\AkademikCalculationService;
use App\Services\AcademicCalendarService;
use App\Services\StudentRiskCalculator;
use App\Services\KrsRecommendationEngine;
use App\Models\Krs;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected AkademikCalculationService $calculationService,
        protected AcademicCalendarService $calendarService,
        protected StudentRiskCalculator $riskCalculator,
        protected KrsRecommendationEngine $recommendationEngine
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Unauthorized');
        }

        $mahasiswa->load(['prodi.fakultas', 'dosenPa.user']);

        // Academic calculations
        $ipkData = $this->calculationService->calculateIPK($mahasiswa);
        $ipsHistory = $this->calculationService->getIPSHistory($mahasiswa);

        // Current semester IPS = last semester with actual grades
        $semestersWithGrades = $ipsHistory->filter(fn($s) => $s['ips'] > 0);
        $lastSemesterWithGrades = $semestersWithGrades->last();
        $currentIps = $lastSemesterWithGrades
            ? ['ips' => $lastSemesterWithGrades['ips'], 'total_sks' => $lastSemesterWithGrades['total_sks']]
            : null;

        // Max SKS for next semester
        $lastIps = $lastSemesterWithGrades['ips'] ?? 0;
        $maxSks = $this->calculationService->getMaxSKS($lastIps);

        // SKS per semester for chart
        $sksHistory = $ipsHistory->map(fn($s) => [
            'semester' => $s['tahun_akademik'],
            'sks' => $s['total_sks'],
        ]);

        // Academic calendar (centralized)
        $activeTA = $this->calendarService->currentPeriod();
        $currentPhase = $this->calendarService->getCurrentPhase();
        $isEnrollmentOpen = $this->calendarService->isEnrollmentOpen();
        $daysUntilEnrollmentCloses = $this->calendarService->daysUntilEnrollmentCloses();

        // Current KRS status
        $currentKrs = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $activeTA?->id)
            ->with('krsDetail.kelas.mataKuliah')
            ->first();

        // Risk profile
        $riskProfile = $this->riskCalculator->calculate($mahasiswa);

        // KRS recommendations
        $krsRecommendation = $this->recommendationEngine->suggest($mahasiswa);

        // Decision cards
        $decisionCards = $this->buildDecisionCards(
            $currentKrs,
            $isEnrollmentOpen,
            $riskProfile,
            $krsRecommendation,
            $daysUntilEnrollmentCloses
        );

        // Grade distribution
        $gradeDistribution = $this->calculationService->getGradeDistribution($mahasiswa);

        // Graduation progress
        $targetSks = config('academic_rules.default.graduation_total_sks', 144);
        $graduationProgress = [
            'current' => $ipkData['total_sks'],
            'target' => $targetSks,
            'percentage' => round(($ipkData['total_sks'] / $targetSks) * 100, 1),
        ];

        // Greeting based on time
        $hour = now()->hour;
        $greeting = match (true) {
            $hour < 11 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };

        return view('mahasiswa.dashboard.index', compact(
            'user',
            'mahasiswa',
            'ipkData',
            'ipsHistory',
            'currentIps',
            'maxSks',
            'sksHistory',
            'currentKrs',
            'gradeDistribution',
            'greeting',
            'activeTA',
            'currentPhase',
            'isEnrollmentOpen',
            'riskProfile',
            'krsRecommendation',
            'decisionCards',
            'graduationProgress'
        ));
    }

    /**
     * Build decision-oriented action cards
     */
    protected function buildDecisionCards(
        ?Krs $currentKrs,
        bool $isEnrollmentOpen,
        $riskProfile,
        $krsRecommendation,
        ?int $daysUntilClose
    ): array {
        $cards = [];

        // KRS action card (highest priority during enrollment)
        if ($isEnrollmentOpen) {
            if (!$currentKrs || $currentKrs->status === 'draft') {
                $cards[] = [
                    'type' => 'action',
                    'priority' => 1,
                    'title' => 'Pengisian KRS',
                    'description' => $daysUntilClose
                        ? "Sisa {$daysUntilClose} hari untuk mengisi KRS"
                        : 'Segera isi KRS Anda',
                    'action' => route('mahasiswa.krs.index'),
                    'action_label' => 'Isi KRS',
                    'color' => 'primary',
                    'icon' => 'plus',
                    'badge' => $currentKrs ? 'Draft' : 'Baru',
                ];
            } elseif ($currentKrs->status === 'rejected') {
                $cards[] = [
                    'type' => 'warning',
                    'priority' => 1,
                    'title' => 'KRS Ditolak',
                    'description' => 'KRS Anda perlu direvisi. Periksa catatan dari Dosen PA.',
                    'action' => route('mahasiswa.krs.index'),
                    'action_label' => 'Revisi KRS',
                    'color' => 'danger',
                    'icon' => 'exclamation',
                ];
            } elseif ($currentKrs->status === 'pending') {
                $cards[] = [
                    'type' => 'info',
                    'priority' => 2,
                    'title' => 'Menunggu Persetujuan',
                    'description' => 'KRS Anda sedang ditinjau oleh Dosen PA.',
                    'action' => route('mahasiswa.krs.index'),
                    'action_label' => 'Lihat Status',
                    'color' => 'warning',
                    'icon' => 'clock',
                ];
            }
        }

        // KRS recommendation card
        if ($krsRecommendation->hasSuggestions() && $isEnrollmentOpen) {
            $suggestedCount = $krsRecommendation->priorityCourses->count();
            if ($suggestedCount > 0) {
                $cards[] = [
                    'type' => 'suggestion',
                    'priority' => 3,
                    'title' => 'Rekomendasi Mata Kuliah',
                    'description' => "{$suggestedCount} mata kuliah direkomendasikan untuk Anda",
                    'action' => route('mahasiswa.krs.index'),
                    'action_label' => 'Lihat Rekomendasi',
                    'color' => 'info',
                    'icon' => 'lightbulb',
                ];
            }
        }

        // Sort by priority
        usort($cards, fn($a, $b) => $a['priority'] <=> $b['priority']);

        return $cards;
    }
}

