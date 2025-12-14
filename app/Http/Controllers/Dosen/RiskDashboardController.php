<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Services\StudentRiskCalculator;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Krs;
use Illuminate\Support\Facades\Auth;

/**
 * Risk Dashboard Controller
 * 
 * Provides Dosen PA with a prioritized view of their advisees
 * sorted by academic risk level.
 */
class RiskDashboardController extends Controller
{
    public function __construct(
        protected StudentRiskCalculator $riskCalculator
    ) {
    }

    /**
     * Display risk dashboard for Dosen PA
     */
    public function index()
    {
        $user = Auth::user();
        $dosen = Dosen::where('user_id', $user->id)->first();

        if (!$dosen) {
            abort(403, 'Unauthorized');
        }

        // Get all advisees (mahasiswa bimbingan PA)
        $advisees = Mahasiswa::where('dosen_pa_id', $dosen->id)
            ->where('status', 'aktif')
            ->with(['user', 'prodi'])
            ->get();

        // Calculate risk for each student
        $studentsWithRisk = $advisees->map(function ($mahasiswa) {
            $riskProfile = $this->riskCalculator->calculate($mahasiswa);

            // Get pending KRS if any
            $pendingKrs = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'pending')
                ->whereHas('tahunAkademik', fn($q) => $q->where('is_active', true))
                ->first();

            return [
                'mahasiswa' => $mahasiswa,
                'risk' => $riskProfile,
                'pending_krs' => $pendingKrs,
                'needs_attention' => $riskProfile->isHighRisk() || $pendingKrs !== null,
            ];
        });

        // Sort by risk score (highest first)
        $studentsWithRisk = $studentsWithRisk->sortByDesc(fn($s) => $s['risk']->score);

        // Group by risk level
        $riskGroups = [
            'critical' => $studentsWithRisk->filter(fn($s) => $s['risk']->level === 'CRITICAL'),
            'high' => $studentsWithRisk->filter(fn($s) => $s['risk']->level === 'HIGH'),
            'medium' => $studentsWithRisk->filter(fn($s) => $s['risk']->level === 'MEDIUM'),
            'low' => $studentsWithRisk->filter(fn($s) => $s['risk']->level === 'LOW'),
        ];

        // Statistics
        $stats = [
            'total_advisees' => $advisees->count(),
            'pending_krs' => $studentsWithRisk->filter(fn($s) => $s['pending_krs'] !== null)->count(),
            'high_risk' => $riskGroups['critical']->count() + $riskGroups['high']->count(),
            'needs_attention' => $studentsWithRisk->filter(fn($s) => $s['needs_attention'])->count(),
        ];

        return view('dosen.risk-dashboard.index', compact(
            'studentsWithRisk',
            'riskGroups',
            'stats'
        ));
    }

    /**
     * Show detailed risk profile for a specific student
     */
    public function show(Mahasiswa $mahasiswa)
    {
        $user = Auth::user();
        $dosen = Dosen::where('user_id', $user->id)->first();

        // Verify this student is under this dosen's supervision
        if ($mahasiswa->dosen_pa_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        $mahasiswa->load(['user', 'prodi.fakultas']);

        $riskProfile = $this->riskCalculator->calculate($mahasiswa);

        // Get KRS history
        $krsHistory = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->with(['tahunAkademik', 'krsDetail.kelas.mataKuliah'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dosen.risk-dashboard.show', compact(
            'mahasiswa',
            'riskProfile',
            'krsHistory'
        ));
    }
}
