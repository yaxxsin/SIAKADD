<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\KrsService;
use App\Services\KrsRecommendationEngine;
use App\Exceptions\EnrollmentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KrsController extends Controller
{
    public function __construct(
        protected KrsService $krsService,
        protected KrsRecommendationEngine $recommendationEngine
    ) {
    }

    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa)
            abort(403, 'Unauthorized');

        $krs = $this->krsService->getActiveKrsOrNew($mahasiswa);

        // Get enrollment status
        $enrollmentStatus = $this->krsService->getEnrollmentStatus($mahasiswa, $krs);

        // Get KRS recommendations
        $recommendations = $this->recommendationEngine->suggest($mahasiswa);

        // Load available classes grouped by semester
        $availableKelas = \App\Models\Kelas::with(['mataKuliah', 'dosen.user', 'krsDetail'])
            ->whereDoesntHave('krsDetail', function ($q) use ($krs) {
                $q->where('krs_id', $krs->id);
            })
            ->get()
            ->groupBy(fn($k) => 'Semester ' . $k->mataKuliah->semester);

        $availableKelas = $availableKelas->sortKeys();

        return view('mahasiswa.krs.index', compact(
            'krs',
            'availableKelas',
            'enrollmentStatus',
            'recommendations'
        ));
    }

    public function store(Request $request)
    {
        $request->validate(['kelas_id' => 'required|exists:kelas,id']);

        $mahasiswa = Auth::user()->mahasiswa;
        $krs = $this->krsService->getActiveKrsOrNew($mahasiswa);

        try {
            $this->krsService->addKelas($mahasiswa, $krs, $request->kelas_id);
            return redirect()->back()->with('success', 'Kelas berhasil diambil');
        } catch (EnrollmentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($detailId)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $krs = $this->krsService->getActiveKrsOrNew($mahasiswa);

        try {
            $this->krsService->removeKelas($krs, $detailId);
            return redirect()->back()->with('success', 'Kelas dibatalkan');
        } catch (EnrollmentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function submit()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $krs = $this->krsService->getActiveKrsOrNew($mahasiswa);

        try {
            $this->krsService->submitKrs($krs);
            return redirect()->back()->with('success', 'KRS berhasil diajukan');
        } catch (EnrollmentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function revise()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $krs = $this->krsService->getActiveKrsOrNew($mahasiswa);

        try {
            $this->krsService->resetToDraft($krs);
            return redirect()->back()->with('success', 'KRS berhasil direset ke draft. Silakan edit dan ajukan kembali.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

