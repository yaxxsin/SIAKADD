<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FakultasController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\TahunAkademikController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KrsApprovalController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Mahasiswa\KrsController;
use App\Http\Controllers\Mahasiswa\TranskripController;
use App\Http\Controllers\Dosen\PenilaianController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // Master Data - Fakultas
    Route::get('/fakultas', [FakultasController::class, 'index'])->name('fakultas.index');
    Route::post('/fakultas', [FakultasController::class, 'store'])->name('fakultas.store');
    Route::put('/fakultas/{fakultas}', [FakultasController::class, 'update'])->name('fakultas.update');
    Route::delete('/fakultas/{fakultas}', [FakultasController::class, 'destroy'])->name('fakultas.destroy');

    // Master Data - Prodi
    Route::get('/prodi', [ProdiController::class, 'index'])->name('prodi.index');
    Route::post('/prodi', [ProdiController::class, 'store'])->name('prodi.store');
    Route::put('/prodi/{prodi}', [ProdiController::class, 'update'])->name('prodi.update');
    Route::delete('/prodi/{prodi}', [ProdiController::class, 'destroy'])->name('prodi.destroy');

    // Master Data - Mata Kuliah
    Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah.index');
    Route::post('/mata-kuliah', [MataKuliahController::class, 'store'])->name('mata-kuliah.store');
    Route::put('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'update'])->name('mata-kuliah.update');
    Route::delete('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('mata-kuliah.destroy');

    // Master Data - Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    Route::get('/tahun-akademik/active', [TahunAkademikController::class, 'getActive'])->name('tahun-akademik.active');
    Route::post('/tahun-akademik/{id}/activate', [TahunAkademikController::class, 'activate'])->name('tahun-akademik.activate');

    // KRS Approval
    Route::get('/krs-approval', [KrsApprovalController::class, 'index'])->name('krs-approval.index');
    Route::get('/krs-approval/{krs}', [KrsApprovalController::class, 'show'])->name('krs-approval.show');
    Route::post('/krs-approval/{krs}/approve', [KrsApprovalController::class, 'approve'])->name('krs-approval.approve');
    Route::post('/krs-approval/{krs}/reject', [KrsApprovalController::class, 'reject'])->name('krs-approval.reject');
    Route::post('/krs-approval/bulk-approve', [KrsApprovalController::class, 'bulkApprove'])->name('krs-approval.bulk-approve');

    // Mahasiswa Management
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::get('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'show'])->name('mahasiswa.show');

    // Dosen Management
    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
    Route::get('/dosen/{dosen}', [DosenController::class, 'show'])->name('dosen.show');

    // Ruangan Management
    Route::get('/ruangan', [\App\Http\Controllers\Admin\RuanganController::class, 'index'])->name('ruangan.index');
    Route::post('/ruangan', [\App\Http\Controllers\Admin\RuanganController::class, 'store'])->name('ruangan.store');
    Route::put('/ruangan/{ruangan}', [\App\Http\Controllers\Admin\RuanganController::class, 'update'])->name('ruangan.update');
    Route::delete('/ruangan/{ruangan}', [\App\Http\Controllers\Admin\RuanganController::class, 'destroy'])->name('ruangan.destroy');

    // Skripsi Management
    Route::get('/skripsi', [\App\Http\Controllers\Admin\SkripsiController::class, 'index'])->name('skripsi.index');
    Route::get('/skripsi/{skripsi}', [\App\Http\Controllers\Admin\SkripsiController::class, 'show'])->name('skripsi.show');
    Route::post('/skripsi/{skripsi}/assign-pembimbing', [\App\Http\Controllers\Admin\SkripsiController::class, 'assignPembimbing'])->name('skripsi.assign-pembimbing');
    Route::put('/skripsi/{skripsi}/status', [\App\Http\Controllers\Admin\SkripsiController::class, 'updateStatus'])->name('skripsi.update-status');
    Route::put('/skripsi/{skripsi}/nilai', [\App\Http\Controllers\Admin\SkripsiController::class, 'updateNilai'])->name('skripsi.update-nilai');

    // KP Management
    Route::get('/kp', [\App\Http\Controllers\Admin\KpController::class, 'index'])->name('kp.index');
    Route::get('/kp/{kp}', [\App\Http\Controllers\Admin\KpController::class, 'show'])->name('kp.show');
    Route::post('/kp/{kp}/assign-pembimbing', [\App\Http\Controllers\Admin\KpController::class, 'assignPembimbing'])->name('kp.assign-pembimbing');
    Route::put('/kp/{kp}/status', [\App\Http\Controllers\Admin\KpController::class, 'updateStatus'])->name('kp.update-status');
    Route::put('/kp/{kp}/nilai', [\App\Http\Controllers\Admin\KpController::class, 'updateNilai'])->name('kp.update-nilai');

    // Kehadiran Dosen
    Route::get('/kehadiran-dosen', [\App\Http\Controllers\Admin\KehadiranDosenController::class, 'index'])->name('kehadiran-dosen.index');
    Route::get('/kehadiran-dosen/{dosen}', [\App\Http\Controllers\Admin\KehadiranDosenController::class, 'show'])->name('kehadiran-dosen.show');
});

// --- MAHASISWA ROUTES ---
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Mahasiswa\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/krs', [KrsController::class, 'index'])->name('krs.index');
    Route::post('/krs', [KrsController::class, 'store'])->name('krs.store');
    Route::delete('/krs/{detailId}', [KrsController::class, 'destroy'])->name('krs.destroy');
    Route::post('/krs/submit', [KrsController::class, 'submit'])->name('krs.submit');
    Route::post('/krs/revise', [KrsController::class, 'revise'])->name('krs.revise');
    
    // Transkrip
    Route::get('/transkrip', [TranskripController::class, 'index'])->name('transkrip.index');
    
    // Biodata
    Route::get('/biodata', [\App\Http\Controllers\Mahasiswa\BiodataController::class, 'index'])->name('biodata.index');
    Route::put('/biodata', [\App\Http\Controllers\Mahasiswa\BiodataController::class, 'update'])->name('biodata.update');
    Route::put('/biodata/password', [\App\Http\Controllers\Mahasiswa\BiodataController::class, 'updatePassword'])->name('biodata.password');

    // Presensi
    Route::get('/presensi', [\App\Http\Controllers\Mahasiswa\PresensiController::class, 'index'])->name('presensi.index');
    Route::get('/presensi/{kelas}', [\App\Http\Controllers\Mahasiswa\PresensiController::class, 'show'])->name('presensi.show');

    // Jadwal Kuliah
    Route::get('/jadwal', [\App\Http\Controllers\Mahasiswa\JadwalController::class, 'index'])->name('jadwal.index');

    // KHS (Kartu Hasil Studi)
    Route::get('/khs', [\App\Http\Controllers\Mahasiswa\KhsController::class, 'index'])->name('khs.index');
    Route::get('/khs/{tahunAkademik}', [\App\Http\Controllers\Mahasiswa\KhsController::class, 'show'])->name('khs.show');

    // Export PDF
    Route::get('/export/transkrip', [\App\Http\Controllers\Mahasiswa\ExportController::class, 'transkrip'])->name('export.transkrip');
    Route::get('/export/khs/{tahunAkademik}', [\App\Http\Controllers\Mahasiswa\ExportController::class, 'khs'])->name('export.khs');

    // Skripsi
    Route::get('/skripsi', [\App\Http\Controllers\Mahasiswa\SkripsiController::class, 'index'])->name('skripsi.index');
    Route::get('/skripsi/create', [\App\Http\Controllers\Mahasiswa\SkripsiController::class, 'create'])->name('skripsi.create');
    Route::post('/skripsi', [\App\Http\Controllers\Mahasiswa\SkripsiController::class, 'store'])->name('skripsi.store');
    Route::post('/skripsi/bimbingan', [\App\Http\Controllers\Mahasiswa\SkripsiController::class, 'storeBimbingan'])->name('skripsi.bimbingan.store');

    // KP
    Route::get('/kp', [\App\Http\Controllers\Mahasiswa\KpController::class, 'index'])->name('kp.index');
    Route::get('/kp/create', [\App\Http\Controllers\Mahasiswa\KpController::class, 'create'])->name('kp.create');
    Route::post('/kp', [\App\Http\Controllers\Mahasiswa\KpController::class, 'store'])->name('kp.store');
    Route::post('/kp/logbook', [\App\Http\Controllers\Mahasiswa\KpController::class, 'storeLogbook'])->name('kp.logbook.store');
});

// --- DOSEN ROUTES ---
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::post('/penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');

    // Bimbingan (Dosen PA)
    Route::get('/bimbingan', [\App\Http\Controllers\Dosen\BimbinganController::class, 'index'])->name('bimbingan.index');
    Route::get('/bimbingan/krs-approval', [\App\Http\Controllers\Dosen\BimbinganController::class, 'krsApproval'])->name('bimbingan.krs-approval');
    Route::get('/bimbingan/krs/{krs}', [\App\Http\Controllers\Dosen\BimbinganController::class, 'showKrs'])->name('bimbingan.krs-show');
    Route::post('/bimbingan/krs/{krs}/approve', [\App\Http\Controllers\Dosen\BimbinganController::class, 'approveKrs'])->name('bimbingan.krs-approve');
    Route::post('/bimbingan/krs/{krs}/reject', [\App\Http\Controllers\Dosen\BimbinganController::class, 'rejectKrs'])->name('bimbingan.krs-reject');

    // Presensi
    Route::get('/presensi', [\App\Http\Controllers\Dosen\PresensiController::class, 'index'])->name('presensi.index');
    Route::get('/presensi/kelas/{kelas}', [\App\Http\Controllers\Dosen\PresensiController::class, 'showKelas'])->name('presensi.kelas');
    Route::get('/presensi/kelas/{kelas}/pertemuan/create', [\App\Http\Controllers\Dosen\PresensiController::class, 'createPertemuan'])->name('presensi.pertemuan.create');
    Route::post('/presensi/kelas/{kelas}/pertemuan', [\App\Http\Controllers\Dosen\PresensiController::class, 'storePertemuan'])->name('presensi.pertemuan.store');
    Route::get('/presensi/pertemuan/{pertemuan}/input', [\App\Http\Controllers\Dosen\PresensiController::class, 'inputPresensi'])->name('presensi.input');
    Route::post('/presensi/pertemuan/{pertemuan}', [\App\Http\Controllers\Dosen\PresensiController::class, 'storePresensi'])->name('presensi.store');

    // Skripsi Bimbingan
    Route::get('/skripsi', [\App\Http\Controllers\Dosen\SkripsiController::class, 'index'])->name('skripsi.index');
    Route::get('/skripsi/{skripsi}', [\App\Http\Controllers\Dosen\SkripsiController::class, 'show'])->name('skripsi.show');
    Route::post('/skripsi/bimbingan/{bimbingan}/review', [\App\Http\Controllers\Dosen\SkripsiController::class, 'reviewBimbingan'])->name('skripsi.bimbingan.review');
    Route::put('/skripsi/{skripsi}/status', [\App\Http\Controllers\Dosen\SkripsiController::class, 'updateStatus'])->name('skripsi.update-status');

    // KP Bimbingan
    Route::get('/kp', [\App\Http\Controllers\Dosen\KpController::class, 'index'])->name('kp.index');
    Route::get('/kp/{kp}', [\App\Http\Controllers\Dosen\KpController::class, 'show'])->name('kp.show');
    Route::post('/kp/logbook/{logbook}/review', [\App\Http\Controllers\Dosen\KpController::class, 'reviewLogbook'])->name('kp.logbook.review');
    Route::put('/kp/{kp}/status', [\App\Http\Controllers\Dosen\KpController::class, 'updateStatus'])->name('kp.update-status');

    // Kehadiran
    Route::get('/kehadiran', [\App\Http\Controllers\Dosen\KehadiranController::class, 'index'])->name('kehadiran.index');
    Route::post('/kehadiran', [\App\Http\Controllers\Dosen\KehadiranController::class, 'store'])->name('kehadiran.store');
    Route::post('/kehadiran/{kehadiran}/checkout', [\App\Http\Controllers\Dosen\KehadiranController::class, 'checkout'])->name('kehadiran.checkout');
    // Penilaian
    Route::get('/penilaian', [\App\Http\Controllers\Dosen\PenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('/penilaian/{kelas}', [\App\Http\Controllers\Dosen\PenilaianController::class, 'show'])->name('penilaian.show');
    Route::post('/penilaian/{kelas}', [\App\Http\Controllers\Dosen\PenilaianController::class, 'store'])->name('penilaian.store');
});

require __DIR__.'/auth.php';
