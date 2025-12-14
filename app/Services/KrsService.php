<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Domain\Enrollment\EnrollmentPolicy;
use Illuminate\Support\Facades\DB;

class KrsService
{
    public function __construct(
        protected EnrollmentPolicy $policy
    ) {
    }

    /**
     * Get or create active KRS for student
     */
    public function getActiveKrsOrNew(Mahasiswa $mahasiswa): Krs
    {
        $tahunAktif = TahunAkademik::where('is_active', true)->first();
        if (!$tahunAktif) {
            throw new \Exception('Tidak ada tahun akademik yang aktif.');
        }

        return Krs::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAktif->id
            ],
            ['status' => 'draft']
        );
    }

    /**
     * Add a class to KRS with full validation
     */
    public function addKelas(Mahasiswa $mahasiswa, Krs $krs, int $kelasId): KrsDetail
    {
        return DB::transaction(function () use ($mahasiswa, $krs, $kelasId) {
            $kelas = Kelas::with('mataKuliah')->findOrFail($kelasId);

            // Run all validations through EnrollmentPolicy
            $this->policy->validateEnrollment($mahasiswa, $krs, $kelas);

            return KrsDetail::create([
                'krs_id' => $krs->id,
                'kelas_id' => $kelasId
            ]);
        });
    }

    /**
     * Remove a class from KRS
     */
    public function removeKelas(Krs $krs, int $detailId): void
    {
        $this->policy->assertKrsEditable($krs);

        $detail = $krs->krsDetail()->findOrFail($detailId);
        $detail->delete();
    }

    /**
     * Submit KRS for approval
     */
    public function submitKrs(Krs $krs): void
    {
        $this->policy->validateSubmission($krs);
        $krs->update(['status' => 'pending']);
    }

    /**
     * Approve KRS (for dosen/admin)
     */
    public function approveKrs(Krs $krs, ?string $catatan = null): void
    {
        $krs->update([
            'status' => 'approved',
            'catatan' => $catatan,
        ]);
    }

    /**
     * Reject KRS (for dosen/admin)
     */
    public function rejectKrs(Krs $krs, ?string $catatan = null): void
    {
        $krs->update([
            'status' => 'rejected',
            'catatan' => $catatan,
        ]);
    }

    /**
     * Reset rejected KRS to draft
     */
    public function resetToDraft(Krs $krs): void
    {
        if ($krs->status !== 'rejected') {
            throw new \Exception('Hanya KRS yang ditolak yang dapat direset.');
        }

        $krs->update([
            'status' => 'draft',
            'catatan' => null,
        ]);
    }

    /**
     * Get enrollment status summary
     */
    public function getEnrollmentStatus(Mahasiswa $mahasiswa, Krs $krs): array
    {
        return $this->policy->getEnrollmentStatus($mahasiswa, $krs);
    }

    /**
     * Get policy instance (for direct access if needed)
     */
    public function getPolicy(): EnrollmentPolicy
    {
        return $this->policy;
    }
}

