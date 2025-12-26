# 🔧 FIXES YANG HARUS DILAKUKAN

Panduan implementasi untuk semua perbaikan yang ditemukan dalam audit.

---

## 🔴 CRITICAL FIXES (Prioritas Tertinggi)

### 1. Add Rate Limiting ke AI Chat Endpoint

**File:** `routes/web.php`  
**Line:** 166  
**Severity:** HIGH  
**Estimasi:** 2 menit

**Current Code:**
```php
Route::post('/ai-advisor/chat', [\App\Http\Controllers\Mahasiswa\AiAdvisorController::class, 'chat'])->name('ai-advisor.chat');
```

**Fix:**
```php
Route::post('/ai-advisor/chat', [\App\Http\Controllers\Mahasiswa\AiAdvisorController::class, 'chat'])
    ->middleware('throttle:10,1') // 10 requests per menit
    ->name('ai-advisor.chat');
```

**Atau untuk lebih strict:**
```php
// Di bootstrap/app.php, tambahkan rate limiter baru:
RateLimiter::for('ai-chat', function (Request $request) {
    return Limit::perMinute(10)
        ->by($request->user()?->id ?: $request->ip())
        ->response(function () {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak permintaan. Silakan tunggu sebentar.'
            ], 429);
        });
});

// Kemudian di routes/web.php:
Route::post('/ai-advisor/chat', ...)
    ->middleware('throttle:ai-chat')
    ->name('ai-advisor.chat');
```

---

### 2. Fix Duplicate WHERE Clause

**File:** `app/Http/Controllers/Admin/KrsApprovalController.php`  
**Lines:** 15-17  
**Severity:** MEDIUM  
**Estimasi:** 1 menit

**Current Code (BUGGY):**
```php
$krsList = Krs::with(['mahasiswa.user', 'mahasiswa.prodi', 'tahunAkademik', 'krsDetail.kelas.mataKuliah'])
    ->when($status !== 'all', fn($q) => $q->where('status', $status))
    ->when($status !== 'all', fn($q) => $q->where('status', $status)); // ❌ DUPLICATE!
```

**Fixed Code:**
```php
$krsList = Krs::with(['mahasiswa.user', 'mahasiswa.prodi', 'tahunAkademik', 'krsDetail.kelas.mataKuliah'])
    ->when($status !== 'all', fn($q) => $q->where('status', $status)); // ✅ Hanya satu kali
```

---

### 3. Add Input Validation untuk Bulk Approve

**File:** `app/Http/Controllers/Admin/KrsApprovalController.php`  
**Method:** `bulkApprove`  
**Line:** 88-100  
**Severity:** MEDIUM  
**Estimasi:** 5 menit

**Current Code (UNSAFE):**
```php
public function bulkApprove(Request $request)
{
    $ids = $request->input('krs_ids', []); // ❌ No validation!
    
    if (empty($ids)) {
        return redirect()->back()->with('error', 'Pilih minimal satu KRS');
    }

    Krs::whereIn('id', $ids)
        ->where('status', 'pending')
        ->update(['status' => 'approved']);

    return redirect()->back()->with('success', count($ids) . ' KRS berhasil disetujui');
}
```

**Fixed Code:**
```php
public function bulkApprove(Request $request)
{
    // ✅ Validate input
    $validated = $request->validate([
        'krs_ids' => 'required|array|min:1|max:100', // Max 100 untuk prevent abuse
        'krs_ids.*' => 'integer|exists:krs,id',
    ]);
    
    $ids = $validated['krs_ids'];

    // Update dan get count yang actual
    $updated = Krs::whereIn('id', $ids)
        ->where('status', 'pending')
        ->update(['status' => 'approved']);

    return redirect()->back()->with('success', $updated . ' KRS berhasil disetujui');
}
```

---

## ⚠️ HIGH PRIORITY FIXES

### 4. Add Database Indexes

**Estimasi:** 15 menit  
**Impact:** HIGH (Performance)

**Create Migration:**
```bash
php artisan make:migration add_performance_indexes_to_tables
```

**File:** `database/migrations/xxxx_add_performance_indexes_to_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KRS table - heavily queried by mahasiswa_id and status
        Schema::table('krs', function (Blueprint $table) {
            $table->index(['mahasiswa_id', 'tahun_akademik_id'], 'krs_mhs_ta_idx');
            $table->index('status', 'krs_status_idx');
        });

        // KRS Detail - always joined with krs
        Schema::table('krs_detail', function (Blueprint $table) {
            $table->index(['krs_id', 'kelas_id'], 'krs_detail_composite_idx');
        });

        // Nilai - frequently queried for transcripts
        Schema::table('nilai', function (Blueprint $table) {
            $table->index(['mahasiswa_id', 'kelas_id'], 'nilai_mhs_kelas_idx');
        });

        // Presensi - attendance reports
        Schema::table('presensi', function (Blueprint $table) {
            $table->index(['mahasiswa_id', 'pertemuan_id'], 'presensi_mhs_pertemuan_idx');
            $table->index('status', 'presensi_status_idx');
        });

        // Kelas - frequently filtered by semester
        Schema::table('kelas', function (Blueprint $table) {
            $table->index('mata_kuliah_id', 'kelas_mk_idx');
            $table->index('dosen_id', 'kelas_dosen_idx');
        });

        // Mahasiswa - filtered by prodi and status
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->index('prodi_id', 'mahasiswa_prodi_idx');
            $table->index('status', 'mahasiswa_status_idx');
            $table->index('angkatan', 'mahasiswa_angkatan_idx');
        });

        // Activity Log - queried by user and date
        Schema::table('activity_log', function (Blueprint $table) {
            $table->index('user_id', 'activity_user_idx');
            $table->index('created_at', 'activity_date_idx');
        });

        // Notifications - heavily queried by user and read status
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read'], 'notifications_user_read_idx');
            $table->index('created_at', 'notifications_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('krs', function (Blueprint $table) {
            $table->dropIndex('krs_mhs_ta_idx');
            $table->dropIndex('krs_status_idx');
        });

        Schema::table('krs_detail', function (Blueprint $table) {
            $table->dropIndex('krs_detail_composite_idx');
        });

        Schema::table('nilai', function (Blueprint $table) {
            $table->dropIndex('nilai_mhs_kelas_idx');
        });

        Schema::table('presensi', function (Blueprint $table) {
            $table->dropIndex('presensi_mhs_pertemuan_idx');
            $table->dropIndex('presensi_status_idx');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropIndex('kelas_mk_idx');
            $table->dropIndex('kelas_dosen_idx');
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropIndex('mahasiswa_prodi_idx');
            $table->dropIndex('mahasiswa_status_idx');
            $table->dropIndex('mahasiswa_angkatan_idx');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('activity_user_idx');
            $table->dropIndex('activity_date_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_idx');
            $table->dropIndex('notifications_date_idx');
        });
    }
};
```

**Run Migration:**
```bash
php artisan migrate
```

---

### 5. Fix N+1 Query Problem - Kelas List

**File:** `app/Http/Controllers/Mahasiswa/KrsController.php`  
**Method:** `index`  
**Lines:** 27-35  
**Severity:** HIGH (Performance)  
**Estimasi:** 20 menit

**Current Code (SLOW):**
```php
// Load available classes (that are not yet taken), grouped by semester
$availableKelas = \App\Models\Kelas::with(['mataKuliah', 'dosen.user', 'krsDetail'])
    ->whereDoesntHave('krsDetail', function($q) use ($krs) {
        $q->where('krs_id', $krs->id);
    })
    ->get() // ❌ Loads ALL kelas!
    ->groupBy(fn($k) => 'Semester ' . $k->mataKuliah->semester);
```

**Fixed Code (FAST):**
```php
// Get semester dari mahasiswa untuk smart filtering
$mahasiswaSemester = $this->calculateCurrentSemester($mahasiswa);

// Load available classes with pagination and optimization
$availableKelas = \App\Models\Kelas::with(['mataKuliah', 'dosen.user'])
    ->withCount('krsDetail') // Instead of loading all krsDetail
    ->whereDoesntHave('krsDetail', function($q) use ($krs) {
        $q->where('krs_id', $krs->id);
    })
    // Smart filter: hanya tampilkan kelas untuk semester yang relevan
    ->whereHas('mataKuliah', function($q) use ($mahasiswaSemester) {
        $q->whereBetween('semester', [
            max(1, $mahasiswaSemester - 1), // Semester sebelumnya
            min(8, $mahasiswaSemester + 2)  // 2 semester ke depan
        ]);
    })
    ->orderBy('mata_kuliah_id')
    ->get() // Still get all untuk grouping, but filtered by semester
    ->groupBy(fn($k) => 'Semester ' . $k->mataKuliah->semester);

// Sort by semester number
$availableKelas = $availableKelas->sortKeys();

// Check capacity before displaying
$availableKelas = $availableKelas->map(function($kelasList) {
    return $kelasList->filter(function($kelas) {
        // Only show kelas that still have capacity
        return $kelas->krs_detail_count < $kelas->kapasitas;
    });
})->filter(fn($list) => $list->isNotEmpty()); // Remove empty semester groups
```

**Add Helper Method:**
```php
// Di controller atau helper
private function calculateCurrentSemester(Mahasiswa $mahasiswa): int
{
    $tahunSekarang = date('Y');
    $tahunMasuk = $mahasiswa->angkatan;
    $selisihTahun = $tahunSekarang - $tahunMasuk;
    
    // Asumsi 2 semester per tahun
    $estimasiSemester = ($selisihTahun * 2) + 1; // +1 karena mulai semester 1
    
    // Atau hitung dari KRS history (lebih akurat)
    $krsApprovedCount = $mahasiswa->krs()
        ->where('status', 'approved')
        ->count();
    
    return max($estimasiSemester, $krsApprovedCount + 1);
}
```

---

### 6. Implement Caching Strategy

**Estimasi:** 30 menit  
**Impact:** HIGH (Performance)

#### 6.1 Cache Tahun Akademik Aktif

**File:** `app/Services/KrsService.php`  
**Method:** `getActiveKrsOrNew`  
**Line:** 17

**Current Code:**
```php
$tahunAktif = TahunAkademik::where('is_active', true)->first();
```

**Fixed Code:**
```php
use Illuminate\Support\Facades\Cache;

$tahunAktif = Cache::remember('tahun_akademik_aktif', 3600, function() {
    return TahunAkademik::where('is_active', true)->first();
});
```

**IMPORTANT:** Clear cache saat update:

**File:** `app/Http/Controllers/Admin/TahunAkademikController.php`

```php
public function activate($id)
{
    // Deactivate all
    TahunAkademik::query()->update(['is_active' => false]);
    
    // Activate selected
    $tahunAkademik = TahunAkademik::findOrFail($id);
    $tahunAkademik->update(['is_active' => true]);
    
    // ✅ Clear cache
    Cache::forget('tahun_akademik_aktif');
    
    return redirect()->back()->with('success', 'Tahun akademik berhasil diaktifkan');
}
```

#### 6.2 Cache Master Data

**Create Service:** `app/Services/CacheService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\TahunAkademik;

class CacheService
{
    const TTL = 3600; // 1 hour
    
    public function getTahunAkademikAktif()
    {
        return Cache::remember('tahun_akademik_aktif', self::TTL, function() {
            return TahunAkademik::where('is_active', true)->first();
        });
    }
    
    public function getAllFakultas()
    {
        return Cache::remember('fakultas_all', self::TTL, function() {
            return Fakultas::with('prodi')->orderBy('nama')->get();
        });
    }
    
    public function getAllProdi()
    {
        return Cache::remember('prodi_all', self::TTL, function() {
            return Prodi::with('fakultas')->orderBy('nama')->get();
        });
    }
    
    public function clearMasterDataCache()
    {
        Cache::forget('tahun_akademik_aktif');
        Cache::forget('fakultas_all');
        Cache::forget('prodi_all');
    }
}
```

**Usage di Controllers:**
```php
use App\Services\CacheService;

public function __construct(protected CacheService $cache)
{
}

public function index()
{
    $fakultas = $this->cache->getAllFakultas();
    // ...
}
```

---

## 📝 MEDIUM PRIORITY FIXES

### 7. Improve Error Handling

**Create Custom Exceptions:**

**File:** `app/Exceptions/KrsException.php`
```php
<?php

namespace App\Exceptions;

use Exception;

class KrsException extends Exception
{
    public static function alreadySubmitted(): self
    {
        return new self('KRS sudah disubmit dan tidak dapat diubah.');
    }
    
    public static function kelasFullyBooked(): self
    {
        return new self('Kelas sudah penuh. Silakan pilih kelas lain.');
    }
    
    public static function subjectAlreadyTaken(): self
    {
        return new self('Mata kuliah ini sudah diambil di KRS Anda.');
    }
    
    public static function sksLimitExceeded(int $current, int $max): self
    {
        return new self("Melebihi batas SKS ({$max}). Total SKS Anda akan menjadi: {$current}");
    }
    
    public static function emptyKrs(): self
    {
        return new self('KRS kosong tidak dapat diajukan.');
    }
}
```

**Update KrsService:**
```php
use App\Exceptions\KrsException;

public function addKelas(Krs $krs, $kelasId)
{
    return DB::transaction(function () use ($krs, $kelasId) {
        if ($krs->status !== 'draft') {
            throw KrsException::alreadySubmitted();
        }

        $kelas = Kelas::with('mataKuliah')->findOrFail($kelasId);
        
        // 1. Cek Kapasitas
        $terisi = KrsDetail::where('kelas_id', $kelasId)->count();
        if ($terisi >= $kelas->kapasitas) {
            throw KrsException::kelasFullyBooked();
        }

        // 2. Cek duplicate
        $mkTaken = $krs->krsDetail()->whereHas('kelas', function($q) use ($kelas) {
            $q->where('mata_kuliah_id', $kelas->mata_kuliah_id);
        })->exists();

        if ($mkTaken) {
            throw KrsException::subjectAlreadyTaken();
        }

        // 3. Cek Batas SKS
        $sksSaatIni = $krs->krsDetail->sum(fn($detail) => $detail->kelas->mataKuliah->sks);
        $sksBaru = $kelas->mataKuliah->sks;
        $maxSks = config('siakad.maks_sks.default', 24); 

        if (($sksSaatIni + $sksBaru) > $maxSks) {
            throw KrsException::sksLimitExceeded($sksSaatIni + $sksBaru, $maxSks);
        }

        return KrsDetail::create([
            'krs_id' => $krs->id,
            'kelas_id' => $kelasId
        ]);
    });
}

public function submitKrs(Krs $krs)
{
    if ($krs->krsDetail()->count() === 0) {
        throw KrsException::emptyKrs();
    }
    $krs->update(['status' => 'pending']);
}
```

**Update Controller:**
```php
use App\Exceptions\KrsException;
use Illuminate\Support\Facades\Log;

public function store(Request $request)
{
    $request->validate(['kelas_id' => 'required|exists:kelas,id']);
    
    $mahasiswa = Auth::user()->mahasiswa;
    $krs = $this->krsService->getActiveKrsOrNew($mahasiswa);

    try {
        $this->krsService->addKelas($krs, $request->kelas_id);
        
        // ✅ Log success
        Log::info('Kelas added to KRS', [
            'mahasiswa_id' => $mahasiswa->id,
            'krs_id' => $krs->id,
            'kelas_id' => $request->kelas_id,
        ]);
        
        return redirect()->back()->with('success', 'Kelas berhasil diambil');
        
    } catch (KrsException $e) {
        // ✅ User-friendly error
        return redirect()->back()->with('error', $e->getMessage());
        
    } catch (\Exception $e) {
        // ✅ Log unexpected errors
        Log::error('Failed to add kelas to KRS', [
            'mahasiswa_id' => $mahasiswa->id,
            'krs_id' => $krs->id,
            'kelas_id' => $request->kelas_id,
            'error' => $e->getMessage(),
        ]);
        
        return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
    }
}
```

---

### 8. Add Comprehensive Logging

**File:** `app/Http/Controllers/Admin/KrsApprovalController.php`

**Add Logging:**
```php
use Illuminate\Support\Facades\Log;

public function approve(Request $request, Krs $krs)
{
    if ($krs->status !== 'pending') {
        return redirect()->back()->with('error', 'KRS tidak dalam status pending');
    }

    $krs->update(['status' => 'approved']);
    
    // ✅ Log business operation
    Log::info('KRS approved', [
        'krs_id' => $krs->id,
        'mahasiswa_nim' => $krs->mahasiswa->nim,
        'mahasiswa_name' => $krs->mahasiswa->user->name,
        'approved_by_id' => Auth::id(),
        'approved_by_name' => Auth::user()->name,
        'total_sks' => $krs->krsDetail->sum(fn($d) => $d->kelas->mataKuliah->sks),
        'timestamp' => now(),
    ]);

    return redirect()->route('admin.krs-approval.index')
        ->with('success', 'KRS mahasiswa ' . $krs->mahasiswa->user->name . ' berhasil disetujui');
}
```

**Add Activity Log Service Usage:**
```php
use App\Services\ActivityLogService;

public function __construct(protected ActivityLogService $activityLog)
{
}

public function approve(Request $request, Krs $krs)
{
    if ($krs->status !== 'pending') {
        return redirect()->back()->with('error', 'KRS tidak dalam status pending');
    }

    $krs->update(['status' => 'approved']);
    
    // ✅ Log to activity log table
    $this->activityLog->log(
        'krs_approved',
        'KRS approved for ' . $krs->mahasiswa->user->name,
        'Krs',
        $krs->id
    );

    return redirect()->route('admin.krs-approval.index')
        ->with('success', 'KRS mahasiswa ' . $krs->mahasiswa->user->name . ' berhasil disetujui');
}
```

---

### 9. Implement IPS-based SKS Calculation

**File:** `app/Services/KrsService.php`  
**Add New Method:**

```php
/**
 * Calculate maximum SKS allowed based on IPS
 */
public function calculateMaxSks(Mahasiswa $mahasiswa): int
{
    // Get IPS semester terakhir
    $lastIps = $this->getLastSemesterIPS($mahasiswa);
    
    if ($lastIps === null) {
        // Mahasiswa baru / belum punya IPS
        return config('siakad.maks_sks.default', 24);
    }
    
    // Get rules dari config
    $ipsRules = config('siakad.maks_sks.ips_rules', []);
    
    foreach ($ipsRules as $rule) {
        if ($lastIps >= $rule['min'] && $lastIps <= $rule['max']) {
            return $rule['sks'];
        }
    }
    
    // Fallback to default
    return config('siakad.maks_sks.default', 24);
}

/**
 * Get IPS semester terakhir yang approved
 */
private function getLastSemesterIPS(Mahasiswa $mahasiswa): ?float
{
    // Get KRS semester lalu yang sudah approved dan ada nilainya
    $lastKrs = Krs::where('mahasiswa_id', $mahasiswa->id)
        ->where('status', 'approved')
        ->whereHas('krsDetail.kelas.nilai', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })
        ->latest('id')
        ->first();
    
    if (!$lastKrs) {
        return null;
    }
    
    // Calculate IPS
    $totalBobot = 0;
    $totalSks = 0;
    
    foreach ($lastKrs->krsDetail as $detail) {
        $nilai = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->where('kelas_id', $detail->kelas_id)
            ->first();
        
        if ($nilai) {
            $sks = $detail->kelas->mataKuliah->sks;
            $bobot = $this->getNilaiBobot($nilai->nilai_huruf);
            
            $totalBobot += ($bobot * $sks);
            $totalSks += $sks;
        }
    }
    
    return $totalSks > 0 ? $totalBobot / $totalSks : null;
}

/**
 * Get bobot dari nilai huruf
 */
private function getNilaiBobot(string $nilaiHuruf): float
{
    $konversi = config('siakad.nilai_konversi', []);
    
    foreach ($konversi as $nilai) {
        if ($nilai['huruf'] === $nilaiHuruf) {
            return $nilai['bobot'];
        }
    }
    
    return 0.0;
}
```

**Update addKelas Method:**
```php
public function addKelas(Krs $krs, $kelasId)
{
    return DB::transaction(function () use ($krs, $kelasId) {
        // ... existing validations ...
        
        // 3. Cek Batas SKS (with IPS calculation)
        $sksSaatIni = $krs->krsDetail->sum(fn($detail) => $detail->kelas->mataKuliah->sks);
        $sksBaru = $kelas->mataKuliah->sks;
        
        // ✅ Calculate based on IPS
        $maxSks = $this->calculateMaxSks($krs->mahasiswa);

        if (($sksSaatIni + $sksBaru) > $maxSks) {
            throw KrsException::sksLimitExceeded($sksSaatIni + $sksBaru, $maxSks);
        }

        // ... rest of method ...
    });
}
```

---

## ✅ NICE TO HAVE IMPROVEMENTS

### 10. Add CSP Headers

**File:** `app/Http/Middleware/SecurityHeadersMiddleware.php`

```php
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    // Existing headers...
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

    // ✅ Add CSP for production
    if (config('app.env') === 'production') {
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net", // Alpine.js needs unsafe-eval
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "font-src 'self' data:",
            "img-src 'self' data: https:",
            "connect-src 'self'",
        ];
        
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
    }

    // HSTS
    if (config('app.env') === 'production' && $request->secure()) {
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    return $response;
}
```

---

### 11. Create Base Controller untuk Mahasiswa

**File:** `app/Http/Controllers/Mahasiswa/BaseController.php`

```php
<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;

abstract class BaseController extends Controller
{
    /**
     * Get authenticated mahasiswa or abort
     */
    protected function getMahasiswa(): Mahasiswa
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            abort(403, 'Unauthorized - Mahasiswa profile not found');
        }
        
        return $mahasiswa;
    }
    
    /**
     * Get mahasiswa with relations
     */
    protected function getMahasiswaWith(array $relations): Mahasiswa
    {
        $mahasiswa = $this->getMahasiswa();
        $mahasiswa->load($relations);
        
        return $mahasiswa;
    }
}
```

**Update Controllers:**
```php
// Before:
class KrsController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) abort(403, 'Unauthorized');
        // ...
    }
}

// After:
class KrsController extends BaseController // ✅ Extend BaseController
{
    public function index()
    {
        $mahasiswa = $this->getMahasiswa(); // ✅ Use helper
        // ...
    }
}
```

---

## 🚀 DEPLOYMENT CHECKLIST

Setelah semua fixes diimplementasi:

### Pre-Deployment

- [ ] Run all tests: `php artisan test`
- [ ] Run migrations on staging: `php artisan migrate`
- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Rebuild assets: `npm run build`
- [ ] Check environment variables
- [ ] Review .env.example for new variables

### Deployment

- [ ] Backup database
- [ ] Put app in maintenance mode: `php artisan down`
- [ ] Pull latest code
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Clear and cache config: `php artisan config:cache`
- [ ] Clear and cache routes: `php artisan route:cache`
- [ ] Clear and cache views: `php artisan view:cache`
- [ ] Rebuild assets: `npm run build`
- [ ] Restart queue workers (if any)
- [ ] Bring app up: `php artisan up`

### Post-Deployment

- [ ] Test critical flows (login, KRS, approval)
- [ ] Check error logs
- [ ] Monitor performance
- [ ] Test rate limiting
- [ ] Verify caching works
- [ ] Check database query performance

---

## 📊 VERIFICATION

Setelah implementasi, verify dengan:

### Test Rate Limiting
```bash
# Test AI endpoint rate limiting
for i in {1..15}; do
  curl -X POST https://your-app.com/mahasiswa/ai-advisor/chat \
    -H "Cookie: your-session-cookie" \
    -d "message=test" && echo " - Request $i"
done
# Should see 429 errors after 10 requests
```

### Test Query Performance
```sql
-- Check if indexes are created
SHOW INDEX FROM krs;
SHOW INDEX FROM krs_detail;
SHOW INDEX FROM nilai;

-- Check query performance
EXPLAIN SELECT * FROM krs 
WHERE mahasiswa_id = 1 AND tahun_akademik_id = 1;
-- Should show "Using index"
```

### Test Caching
```php
// In tinker: php artisan tinker
Cache::forget('tahun_akademik_aktif');
$start = microtime(true);
$ta = app(CacheService::class)->getTahunAkademikAktif();
$firstCall = microtime(true) - $start;

$start = microtime(true);
$ta = app(CacheService::class)->getTahunAkademikAktif();
$secondCall = microtime(true) - $start;

echo "First call: {$firstCall}s\n";
echo "Second call (cached): {$secondCall}s\n";
// Second call should be much faster
```

---

## 🎯 SUMMARY

Total Fixes: **11**
- Critical: **3** (must do sekarang)
- High Priority: **3** (this week)
- Medium Priority: **3** (this month)
- Nice to Have: **2** (when time permits)

**Estimasi Total Waktu:** 2-3 hari kerja

**Expected Impact:**
- ✅ Security: Improved dari 8.3/10 ke 9.0/10
- ✅ Performance: Improved dari 6.2/10 ke 8.5/10
- ✅ Code Quality: Improved dari 8.0/10 ke 9.0/10

**Overall Score:** 8.2/10 → **9.2/10** ⭐⭐⭐⭐⭐

---

*Untuk pertanyaan atau bantuan implementasi, silakan diskusikan dengan tech lead.*
