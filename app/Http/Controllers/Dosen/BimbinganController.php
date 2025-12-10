<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BimbinganController extends Controller
{
    /**
     * Daftar mahasiswa bimbingan
     */
    public function index(Request $request)
    {
        $dosen = Auth::user()->dosen;
        
        if (!$dosen) {
            abort(403, 'Unauthorized');
        }

        $query = $dosen->mahasiswaBimbingan()
            ->with(['user', 'prodi', 'krs' => fn($q) => $q->latest()]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter angkatan
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswaBimbingan = $query->orderBy('angkatan', 'desc')->paginate(10);

        // Get available angkatan for filter
        $angkatanList = $dosen->mahasiswaBimbingan()->distinct()->pluck('angkatan')->sort()->reverse();

        if ($request->ajax()) {
            return view('dosen.bimbingan._table', compact('mahasiswaBimbingan'))->render();
        }

        return view('dosen.bimbingan.index', compact('mahasiswaBimbingan', 'angkatanList'));
    }

    /**
     * Daftar KRS yang perlu diapprove
     */
    public function krsApproval(Request $request)
    {
        $dosen = Auth::user()->dosen;
        
        if (!$dosen) {
            abort(403, 'Unauthorized');
        }

        $status = $request->get('status', 'pending');

        // Get KRS from mahasiswa bimbingan
        $mahasiswaIds = $dosen->mahasiswaBimbingan()->pluck('id');

        $krsList = Krs::with(['mahasiswa.user', 'mahasiswa.prodi', 'tahunAkademik', 'krsDetail.kelas.mataKuliah'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->orderBy('updated_at', 'desc')
            ->paginate(config('siakad.pagination', 15));

        $statusCounts = [
            'pending' => Krs::whereIn('mahasiswa_id', $mahasiswaIds)->where('status', 'pending')->count(),
            'approved' => Krs::whereIn('mahasiswa_id', $mahasiswaIds)->where('status', 'approved')->count(),
            'rejected' => Krs::whereIn('mahasiswa_id', $mahasiswaIds)->where('status', 'rejected')->count(),
        ];

        return view('dosen.bimbingan.krs-approval', compact('krsList', 'status', 'statusCounts'));
    }

    /**
     * Detail KRS
     */
    public function showKrs(Krs $krs)
    {
        $dosen = Auth::user()->dosen;
        
        // Verify this mahasiswa is under this dosen's supervision
        if ($krs->mahasiswa->dosen_pa_id !== $dosen->id) {
            abort(403, 'Anda tidak berhak mengakses KRS ini');
        }

        $krs->load(['mahasiswa.user', 'mahasiswa.prodi', 'tahunAkademik', 'krsDetail.kelas.mataKuliah', 'krsDetail.kelas.dosen.user']);
        
        $totalSks = $krs->krsDetail->sum(fn($d) => $d->kelas->mataKuliah->sks);

        return view('dosen.bimbingan.krs-show', compact('krs', 'totalSks'));
    }

    /**
     * Approve KRS
     */
    public function approveKrs(Krs $krs)
    {
        $dosen = Auth::user()->dosen;
        
        if ($krs->mahasiswa->dosen_pa_id !== $dosen->id) {
            abort(403, 'Anda tidak berhak mengakses KRS ini');
        }

        if ($krs->status !== 'pending') {
            return redirect()->back()->with('error', 'KRS tidak dalam status pending');
        }

        $krs->update(['status' => 'approved']);

        return redirect()->route('dosen.bimbingan.krs-approval')
            ->with('success', 'KRS mahasiswa ' . $krs->mahasiswa->user->name . ' berhasil disetujui');
    }

    /**
     * Reject KRS
     */
    public function rejectKrs(Request $request, Krs $krs)
    {
        $dosen = Auth::user()->dosen;
        
        if ($krs->mahasiswa->dosen_pa_id !== $dosen->id) {
            abort(403, 'Anda tidak berhak mengakses KRS ini');
        }

        if ($krs->status !== 'pending') {
            return redirect()->back()->with('error', 'KRS tidak dalam status pending');
        }

        $catatan = $request->input('catatan', 'KRS ditolak oleh Dosen PA. Silakan revisi dan ajukan kembali.');
        $krs->update(['status' => 'rejected', 'catatan' => $catatan]);

        return redirect()->route('dosen.bimbingan.krs-approval')
            ->with('success', 'KRS mahasiswa ' . $krs->mahasiswa->user->name . ' ditolak');
    }
}
