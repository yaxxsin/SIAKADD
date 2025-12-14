# Changelog

All notable changes to SIAKAD will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-12-14

### Added

#### Core Features
- **Multi-role system** - Admin, Dosen, Mahasiswa dengan dashboard terpisah
- **Master Data Management** - CRUD Fakultas, Prodi, Mata Kuliah, Kelas, Ruangan
- **Academic Year Management** - Tahun akademik dengan semester aktif

#### KRS (Kartu Rencana Studi)
- Pengisian KRS dengan validasi SKS berdasarkan IPS
- Submit dan approval workflow (Draft → Pending → Approved/Rejected)
- Bulk approval untuk admin/dosen PA
- Rate limiting pada operasi KRS

#### Penilaian
- Input nilai oleh dosen dengan auto grade conversion
- Komponen nilai (Tugas, UTS, UAS)
- View KHS per semester untuk mahasiswa
- Transkrip nilai kumulatif dengan IPK

#### Presensi
- Rekap presensi per kelas
- Input presensi per pertemuan oleh dosen
- View presensi mahasiswa

#### Skripsi & Kerja Praktek
- Pengajuan skripsi/KP oleh mahasiswa
- Assignment pembimbing oleh admin
- Logbook bimbingan dengan status tracking
- Progress monitoring untuk dosen pembimbing

#### Security
- Security headers middleware (HSTS, XSS, Clickjacking protection)
- Role-based access control dengan middleware
- Rate limiting pada endpoint sensitif
- Request logging middleware

#### Monitoring
- Health check endpoints (`/health`, `/health/detailed`)
- Activity logging untuk audit trail

#### DevOps
- GitHub Actions CI pipeline
- Automated testing dengan Pest
- Database migrations dan seeders

### Technical Stack
- Laravel 12 / PHP 8.2
- Blade + Alpine.js + Tailwind CSS
- MySQL 8.0 (SQLite untuk development)
- Pest PHP untuk testing

---

## [Unreleased]

### Planned
- [ ] Notifikasi real-time (WebSocket)
- [ ] Export PDF untuk transkrip
- [ ] Mobile-responsive improvements
- [ ] Multi-language support (EN/ID)
- [ ] Advanced reporting dashboard
