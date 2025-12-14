# 📚 API Reference

Dokumentasi endpoint internal SIAKAD. Semua endpoint memerlukan autentikasi kecuali yang ditandai public.

## 🏥 Health Endpoints (Public)

### GET /health
Basic health check.

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2025-12-14T09:00:00+07:00",
  "app": "SIAKAD",
  "environment": "production"
}
```

### GET /health/detailed
Detailed health check dengan dependency status.

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2025-12-14T09:00:00+07:00",
  "checks": {
    "database": { "status": "ok", "latency_ms": 2.5, "connection": "mysql" },
    "cache": { "status": "ok", "driver": "redis" },
    "storage": { "status": "ok", "path": "/var/www/siakad/storage" }
  },
  "version": "1.0.0"
}
```

---

## 🔐 Authentication

### POST /login
Authenticate user.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password",
  "remember": true
}
```

### POST /logout
End user session.

### POST /forgot-password
Request password reset link.

### POST /reset-password
Reset password with token.

---

## 👨‍💼 Admin Endpoints

Semua endpoint memerlukan `role:admin` middleware.

### Master Data - Fakultas

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/fakultas` | List all fakultas |
| POST | `/admin/fakultas` | Create fakultas |
| PUT | `/admin/fakultas/{id}` | Update fakultas |
| DELETE | `/admin/fakultas/{id}` | Delete fakultas |

### Master Data - Prodi

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/prodi` | List all prodi |
| POST | `/admin/prodi` | Create prodi |
| PUT | `/admin/prodi/{id}` | Update prodi |
| DELETE | `/admin/prodi/{id}` | Delete prodi |

### KRS Approval

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/krs-approval` | List pending KRS |
| GET | `/admin/krs-approval/{id}` | View KRS detail |
| POST | `/admin/krs-approval/{id}/approve` | Approve KRS |
| POST | `/admin/krs-approval/{id}/reject` | Reject KRS |
| POST | `/admin/krs-approval/bulk-approve` | Bulk approve |

---

## 👨‍🏫 Dosen Endpoints

Semua endpoint memerlukan `role:dosen` middleware.

### Dashboard
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dosen/dashboard` | Dosen dashboard |

### Penilaian
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dosen/penilaian` | List kelas mengajar |
| GET | `/dosen/penilaian/{kelas}` | Form input nilai |
| POST | `/dosen/penilaian/{kelas}` | Submit nilai |

### Presensi
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dosen/presensi` | List presensi kelas |
| POST | `/dosen/presensi/kelas/{kelas}/pertemuan` | Create pertemuan |
| POST | `/dosen/presensi/pertemuan/{pertemuan}` | Input presensi |

---

## 👨‍🎓 Mahasiswa Endpoints

Semua endpoint memerlukan `role:mahasiswa` middleware.

### KRS
| Method | Endpoint | Description | Rate Limit |
|--------|----------|-------------|------------|
| GET | `/mahasiswa/krs` | View KRS | - |
| POST | `/mahasiswa/krs` | Add mata kuliah | 10/min |
| DELETE | `/mahasiswa/krs/{id}` | Remove mata kuliah | 10/min |
| POST | `/mahasiswa/krs/submit` | Submit KRS | 10/min |

### Akademik
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/mahasiswa/transkrip` | View transkrip |
| GET | `/mahasiswa/khs` | List KHS per semester |
| GET | `/mahasiswa/jadwal` | Jadwal kuliah |
| GET | `/mahasiswa/presensi` | Rekap presensi |

---

## ⚠️ Error Responses

### Standard Error Format
```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthenticated |
| 403 | Unauthorized (wrong role) |
| 404 | Not found |
| 422 | Validation error |
| 429 | Too many requests (rate limited) |
| 500 | Server error |

---

## 🔒 Rate Limiting

| Limiter | Limit | Scope |
|---------|-------|-------|
| `krs` | 10 req/min | Per user |
| `penilaian` | 20 req/min | Per user |
| `sensitive` | 30 req/min | Per user |

Response header saat rate limited:
```
Retry-After: 60
X-RateLimit-Limit: 10
X-RateLimit-Remaining: 0
```
