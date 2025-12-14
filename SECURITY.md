# 🔒 Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

---

## Reporting a Vulnerability

Jika Anda menemukan kerentanan keamanan, **JANGAN** membuka issue publik.

### Cara Melaporkan

1. **Email**: security@siakad.example.com
2. **Subject**: `[SECURITY] Brief description`
3. **Include**:
   - Deskripsi vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (jika ada)

### Response Time

| Severity | Initial Response | Resolution Target |
|----------|-----------------|-------------------|
| Critical | 24 hours | 48 hours |
| High | 48 hours | 1 week |
| Medium | 1 week | 2 weeks |
| Low | 2 weeks | 1 month |

---

## Security Measures

### Authentication & Authorization

- ✅ **Bcrypt hashing** dengan 12 rounds
- ✅ **Role-based access control** (admin, dosen, mahasiswa)
- ✅ **Session-based auth** dengan database driver
- ✅ **Password reset** dengan expiring tokens (60 min)
- ✅ **Login throttling** (5 attempts/minute)

### Request Protection

- ✅ **CSRF protection** pada semua forms
- ✅ **Rate limiting**:
  - KRS operations: 10 req/min
  - Penilaian: 20 req/min
  - General API: 60 req/min

### Headers

Security headers yang diimplementasikan:

```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
Strict-Transport-Security: max-age=31536000; includeSubDomains (HTTPS only)
```

### Data Protection

- ✅ **SQL injection prevention** via Eloquent ORM
- ✅ **XSS prevention** via Blade escaping `{{ }}`
- ✅ **Mass assignment protection** via `$fillable`
- ✅ **Session encryption** (configurable)

### Logging & Monitoring

- ✅ **Request logging** middleware
- ✅ **Activity logging** for sensitive operations
- ✅ **Daily log rotation** (14 days retention)

---

## Security Checklist for Production

```bash
# Environment
APP_DEBUG=false
APP_ENV=production

# Session
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# HTTPS
HTTPS enforced via Nginx/Apache
```

---

## Known Security Considerations

### Third-party Dependencies

Dependencies di-update secara regular. Run:
```bash
composer audit
npm audit
```

### File Uploads

Saat ini tidak ada file upload feature. Jika ditambahkan:
- Validate MIME types
- Limit file sizes
- Store outside public directory
- Scan for malware

---

## Security Best Practices for Contributors

1. **Never commit secrets** ke repository
2. **Use prepared statements** (Eloquent handles this)
3. **Validate all input** dengan Form Requests
4. **Sanitize output** dengan Blade escaping
5. **Follow principle of least privilege**

---

## Acknowledgments

Terima kasih kepada para security researcher yang telah membantu meningkatkan keamanan SIAKAD.
