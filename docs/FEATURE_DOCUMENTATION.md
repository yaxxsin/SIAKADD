# 📚 SIAKAD - Dokumentasi Fitur Lengkap

**Sistem Informasi Akademik dengan Intelligent Academic Advisor**  
**Universitas Riau - Program Studi Sistem Informasi**

---

## 🎯 Ringkasan Sistem

SIAKAD adalah sistem informasi akademik terintegrasi yang dilengkapi dengan **Intelligent Academic Advisor** berbasis **Retrieval-Augmented Generation (RAG)** dengan mekanisme **Guardrails** untuk personalisasi bimbingan akademik.

### Statistik Proyek

| Komponen | Jumlah |
|----------|--------|
| **Models** | 22 |
| **Services** | 14 |
| **Controllers** | 35+ |
| **Blade Views** | 50+ |
| **Database Tables** | 22 |

---

## 🤖 1. INTELLIGENT ACADEMIC ADVISOR (Fitur Utama)

### 1.1 Arsitektur AI Advisor

```
┌─────────────────────────────────────────────────────────────┐
│                    AI ADVISOR SYSTEM                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐    ┌──────────────────┐    ┌───────────┐  │
│  │   Mahasiswa  │───▶│ AdvisorContext   │───▶│  Gemini   │  │
│  │   Question   │    │    Builder       │    │   LLM     │  │
│  └──────────────┘    │     (RAG)        │    └─────┬─────┘  │
│                      └──────────────────┘          │        │
│                                                    ▼        │
│  ┌──────────────┐    ┌──────────────────┐    ┌───────────┐  │
│  │   Response   │◀───│  AdvisorGuards   │◀───│   Raw     │  │
│  │   (Valid)    │    │  (Validation)    │    │  Output   │  │
│  └──────────────┘    └──────────────────┘    └───────────┘  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Komponen RAG (Retrieval-Augmented Generation)

**File:** `app/Services/AcademicAdvisor/AdvisorContextBuilder.php`

| Method | Fungsi | Data yang Diambil |
|--------|--------|-------------------|
| `build()` | Membangun konteks lengkap | Semua data akademik mahasiswa |
| `buildAcademicSummary()` | Ringkasan akademik | IPK, IPS, SKS lulus, semester aktif |
| `buildCourseStatuses()` | Status mata kuliah | LULUS, SEDANG_DIAMBIL, TERSEDIA |
| `getActiveSchedule()` | Jadwal aktif | Hari, jam, ruangan, dosen |
| `getAttendanceData()` | Data presensi | Persentase kehadiran per MK |
| `getProdiRules()` | Aturan akademik | SKS lulus, syarat KP, syarat skripsi |
| `getCurriculum()` | Kurikulum prodi | Daftar MK per semester |
| `calculateGraduationProgress()` | Progress kelulusan | Persentase SKS tercapai |

**Contoh Context JSON yang Dihasilkan:**
```json
{
  "student": {
    "nama": "Ryanda Valents Anakri",
    "nim": "2303113649",
    "prodi": "Sistem Informasi",
    "semester_aktif": 3
  },
  "academic_summary": {
    "ipk": 3.45,
    "ips_terakhir": 3.50,
    "total_sks_lulus": 48,
    "sks_sedang_diambil": 21
  },
  "course_statuses": [...],
  "attendance": {...},
  "prodi_rules": {
    "graduation_total_sks": 144,
    "kp_min_sks": 90,
    "thesis_min_sks": 110
  }
}
```

### 1.3 Mekanisme Guardrails

**File:** `app/Services/AcademicAdvisor/AdvisorGuards.php`

#### Pre-Guards (Sebelum LLM)

| Guard | Fungsi |
|-------|--------|
| `assertRulesPresent()` | Memastikan aturan akademik tersedia |
| `validateContext()` | Validasi kelengkapan konteks |

#### Post-Guards (Setelah LLM)

| Guard | Fungsi |
|-------|--------|
| `preventGenericAssumptions()` | Mencegah asumsi generik |
| `attendanceGuard()` | Validasi klaim kehadiran |
| `runPostGuards()` | Jalankan semua post-guards |

#### Forbidden Phrases (Frasa Terlarang)
```php
protected array $forbiddenPhrases = [
    'biasanya mahasiswa',
    'pada umumnya',
    'menurut pengalaman saya',
    'saya asumsikan',
    // ... dan lainnya
];
```

#### Retry Mechanism
```php
if (!$guardResult['passed'] && $guardResult['should_retry']) {
    $retryResponse = $this->retryWithGuardPrompt(...);
    // Mencoba ulang dengan prompt perbaikan
}
```

### 1.4 AI Advisor Service

**File:** `app/Services/AiAdvisorService.php`

| Method | Fungsi |
|--------|--------|
| `chat()` | Main entry point untuk chat |
| `buildSystemPrompt()` | Membangun system prompt dari template |
| `callLlm()` | Memanggil Gemini API |
| `retryWithGuardPrompt()` | Retry jika guard gagal |

**Alur Kerja:**
1. User mengirim pertanyaan
2. `AdvisorContextBuilder` mengambil data akademik (RAG)
3. Pre-guards memvalidasi konteks
4. LLM menghasilkan respons
5. Post-guards memvalidasi output
6. Jika gagal, retry atau berikan replacement

---

## 📊 2. STUDENT RISK CALCULATOR

**File:** `app/Services/StudentRiskCalculator.php`

### 2.1 Faktor Risiko (5 Faktor)

| Faktor | Bobot | Deskripsi |
|--------|-------|-----------|
| `ips_trend` | 25% | Tren IPS (naik/turun/stabil) |
| `attendance` | 20% | Tingkat kehadiran |
| `retakes` | 15% | Jumlah MK yang diulang |
| `graduation_progress` | 25% | Progress menuju kelulusan |
| `workload` | 15% | Beban SKS (over/under loading) |

### 2.2 Risk Levels

| Score | Level | Warna |
|-------|-------|-------|
| 0-30 | LOW | Hijau |
| 31-50 | MEDIUM | Kuning |
| 51-75 | HIGH | Orange |
| 76-100 | CRITICAL | Merah |

### 2.3 Risk Flags

- `DECLINING_PERFORMANCE` - IPS menurun
- `ATTENDANCE_WARNING` - Kehadiran di bawah 80%
- `HIGH_RETAKE_COUNT` - Banyak MK mengulang
- `GRADUATION_DELAY_RISK` - Risiko terlambat lulus
- `OVERLOADED` - SKS terlalu banyak
- `UNDERLOADED` - SKS terlalu sedikit

### 2.4 Output (RiskProfile DTO)

**File:** `app/DTOs/RiskProfile.php`

```php
readonly class RiskProfile {
    public int $score;           // 0-100
    public string $level;        // LOW/MEDIUM/HIGH/CRITICAL
    public array $factors;       // 5 faktor risiko
    public array $flags;         // Peringatan aktif
    public array $recommendations; // Rekomendasi aksi
}
```

---

## 📅 3. ACADEMIC CALENDAR SERVICE

**File:** `app/Services/AcademicCalendarService.php`

| Method | Fungsi |
|--------|--------|
| `currentPeriod()` | Tahun akademik aktif |
| `getCurrentPhase()` | Fase saat ini (Pengisian KRS, UTS, dll) |
| `isEnrollmentOpen()` | Cek apakah KRS bisa diisi |
| `daysUntilEnrollmentCloses()` | Sisa hari pengisian KRS |
| `calculateStudentSemester()` | Hitung semester mahasiswa |

---

## 💡 4. KRS RECOMMENDATION ENGINE

**File:** `app/Services/KrsRecommendationEngine.php`

### 4.1 Prioritas Rekomendasi

| Prioritas | Jenis | Deskripsi |
|-----------|-------|-----------|
| 1 | Mengulang | MK dengan nilai D/E |
| 2 | Wajib | MK wajib semester saat ini |
| 3 | Pilihan | MK pilihan/semester berikutnya |

### 4.2 Output (KrsSuggestion DTO)

**File:** `app/DTOs/KrsSuggestion.php`

```php
readonly class KrsSuggestion {
    public Collection $priorityCourses;  // MK prioritas
    public Collection $optionalCourses;  // MK opsional
    public array $warnings;              // Peringatan
    public int $remainingSks;            // Sisa SKS
    public int $maxSks;                  // Max SKS diizinkan
}
```

---

## 🏗️ 5. DOMAIN LAYER

### 5.1 Enrollment Policy

**File:** `app/Domain/Enrollment/EnrollmentPolicy.php`

| Method | Fungsi |
|--------|--------|
| `validateEnrollment()` | Validasi lengkap enrollment |
| `assertEnrollmentOpen()` | Cek periode KRS buka |
| `assertClassHasCapacity()` | Cek kapasitas kelas |
| `assertCourseNotDuplicate()` | Cek tidak duplikat |
| `assertSksWithinLimit()` | Cek batas SKS |
| `assertNoScheduleConflict()` | Cek bentrokan jadwal |
| `assertPrerequisitesMet()` | Cek prasyarat |

### 5.2 Enrollment Exception

**File:** `app/Exceptions/EnrollmentException.php`

```php
EnrollmentException::enrollmentClosed()
EnrollmentException::sksLimitExceeded()
EnrollmentException::classFull()
EnrollmentException::duplicateCourse()
EnrollmentException::scheduleConflict()
EnrollmentException::prerequisiteNotMet()
```

### 5.3 Curriculum Versioning

| File | Fungsi |
|------|--------|
| `app/Domain/Curriculum/CurriculumVersion.php` | Representasi kurikulum versi tertentu |
| `app/Domain/Curriculum/CurriculumRepository.php` | Manajemen dan caching kurikulum |

---

## 👨‍🎓 6. MODUL MAHASISWA

### 6.1 Dashboard
- IPK dan IPS terkini
- Distribusi nilai
- Decision Cards (aksi prioritas)
- Progress kelulusan
- Rekomendasi mata kuliah

### 6.2 KRS (Kartu Rencana Studi)
- Tambah kelas dengan validasi EnrollmentPolicy
- Hapus kelas dari KRS aktif
- Submit KRS (ajukan ke Dosen PA)
- Revisi KRS jika ditolak

### 6.3 KHS (Kartu Hasil Studi)
- Lihat nilai per semester
- Export PDF

### 6.4 Transkrip
- Transkrip lengkap
- Export PDF

### 6.5 Presensi
- Lihat presensi per mata kuliah
- Persentase kehadiran

### 6.6 Jadwal Kuliah
- Jadwal mingguan
- Jadwal hari ini

### 6.7 AI Academic Advisor
- Chat dengan AI
- Personalisasi berdasarkan data akademik

### 6.8 Skripsi
- Daftar skripsi
- Catat bimbingan
- Lihat progress

### 6.9 Kerja Praktek (KP)
- Daftar KP
- Logbook harian
- Lihat status

---

## 👨‍🏫 7. MODUL DOSEN

### 7.1 Dashboard
- Statistik mengajar
- Mahasiswa bimbingan

### 7.2 Risk Dashboard (Dosen PA)
- Mahasiswa diurutkan berdasarkan risiko
- Detail profil risiko
- Rekomendasi intervensi

### 7.3 Bimbingan PA
- Daftar mahasiswa bimbingan
- Approve/Reject KRS
- Lihat detail KRS

### 7.4 Presensi (Input)
- Buat pertemuan
- Input presensi mahasiswa

### 7.5 Penilaian
- Input nilai per kelas
- Komponen nilai (UTS, UAS, tugas)

### 7.6 Kehadiran Dosen
- Check-in mengajar
- Riwayat kehadiran

### 7.7 Bimbingan Skripsi
- Daftar bimbingan skripsi
- Review catatan bimbingan

### 7.8 Bimbingan KP
- Daftar bimbingan KP
- Review logbook

---

## 🔧 8. MODUL ADMIN

### 8.1 Master Data

| Controller | Fitur |
|------------|-------|
| `FakultasController` | CRUD Fakultas |
| `ProdiController` | CRUD Program Studi |
| `DosenController` | CRUD Dosen |
| `MahasiswaController` | CRUD Mahasiswa |
| `MataKuliahController` | CRUD Mata Kuliah |
| `KelasController` | CRUD Kelas + Jadwal |
| `RuanganController` | CRUD Ruangan |
| `TahunAkademikController` | Kelola Tahun Akademik |

### 8.2 Approval

| Controller | Fitur |
|------------|-------|
| `KrsApprovalController` | Approve/Reject KRS (bulk) |
| `SkripsiController` | Approval skripsi |
| `KpController` | Approval KP |

### 8.3 Monitoring

| Controller | Fitur |
|------------|-------|
| `KehadiranDosenController` | Monitor kehadiran dosen |
| `DashboardController` | Statistik sistem |

---

## 🗄️ 9. DATABASE MODELS (22 Models)

| Model | Tabel | Deskripsi |
|-------|-------|-----------|
| `User` | users | Akun pengguna |
| `Mahasiswa` | mahasiswa | Data mahasiswa |
| `Dosen` | dosen | Data dosen |
| `Fakultas` | fakultas | Fakultas |
| `Prodi` | prodi | Program studi |
| `MataKuliah` | mata_kuliah | Mata kuliah |
| `Kelas` | kelas | Kelas perkuliahan |
| `JadwalKuliah` | jadwal_kuliah | Jadwal |
| `TahunAkademik` | tahun_akademik | Periode akademik |
| `Krs` | krs | KRS mahasiswa |
| `KrsDetail` | krs_detail | Detail KRS |
| `Nilai` | nilai | Nilai mahasiswa |
| `Pertemuan` | pertemuan | Pertemuan kelas |
| `Presensi` | presensi | Presensi mahasiswa |
| `Ruangan` | ruangan | Ruangan |
| `Skripsi` | skripsi | Data skripsi |
| `BimbinganSkripsi` | bimbingan_skripsi | Catatan bimbingan |
| `KerjaPraktek` | kerja_praktek | Data KP |
| `LogbookKp` | logbook_kp | Logbook KP |
| `KehadiranDosen` | kehadiran_dosen | Kehadiran dosen |
| `Notification` | notifications | Notifikasi |
| `ActivityLog` | activity_logs | Log aktivitas |

---

## 🎨 10. UI COMPONENTS

### 10.1 Blade Components

| Component | Lokasi | Fungsi |
|-----------|--------|--------|
| `x-ui.card` | `components/ui/card.blade.php` | Card wrapper |
| `x-ui.badge` | `components/ui/badge.blade.php` | Status badge |
| `x-ui.progress` | `components/ui/progress.blade.php` | Progress bar |
| `x-ui.metric` | `components/ui/metric.blade.php` | Metric display |
| `x-ui.alert` | `components/ui/alert.blade.php` | Alert messages |
| `x-ui.decision-card` | `components/ui/decision-card.blade.php` | Action card |

### 10.2 Tech Stack

- **Frontend:** Blade, Alpine.js, Tailwind CSS
- **Backend:** Laravel 12, PHP 8.3
- **Database:** MySQL 8.0
- **AI:** Google Gemini API (gemini-2.5-flash)

---

## 🔐 11. SECURITY & MIDDLEWARE

| Middleware | Fungsi |
|------------|--------|
| `auth` | Autentikasi user |
| `role:mahasiswa` | Akses mahasiswa only |
| `role:dosen` | Akses dosen only |
| `role:admin` | Akses admin only |
| `throttle:krs` | Rate limiting KRS |

---

## 📊 12. KESESUAIAN DENGAN PROPOSAL PENELITIAN

### Judul Proposal:
**"Pengembangan Intelligent Academic Advisor Berbasis Retrieval-Augmented Generation (RAG) dengan Mekanisme Guardrails untuk Personalisasi Bimbingan Akademik"**

### Mapping Fitur ke Proposal:

| Aspek Proposal | Implementasi | Status |
|----------------|--------------|--------|
| **Intelligent Academic Advisor** | AiAdvisorService + Chat Interface | ✅ |
| **RAG** | AdvisorContextBuilder (retrieves academic data) | ✅ |
| **Guardrails** | AdvisorGuards (pre + post guards) | ✅ |
| **Personalisasi** | Context berbasis data individual | ✅ |
| **Integrasi SIAKAD** | Full integration with KRS, Nilai, etc. | ✅ |

### Fitur Pendukung Proposal:

1. **StudentRiskCalculator** - Identifikasi mahasiswa berisiko
2. **KrsRecommendationEngine** - Rekomendasi mata kuliah
3. **AcademicCalendarService** - Awareness periode akademik
4. **EnrollmentPolicy** - Business rules yang konsisten

---

## 📝 Catatan

Dokumentasi ini mencakup **semua fitur utama** yang diimplementasikan dalam proyek SIAKAD. Fitur-fitur ini mendukung penelitian tentang **Intelligent Academic Advisor** dengan pendekatan **RAG** dan **Guardrails**.

**Total Lines of Code:** ~15,000+  
**Total Files:** 100+  
**Development Time:** Ongoing

---

*Dokumentasi dibuat: 14 Desember 2024*
