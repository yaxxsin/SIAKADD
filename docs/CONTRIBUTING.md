# 🤝 Contributing to SIAKAD

Terima kasih atas minat Anda untuk berkontribusi! Dokumen ini menjelaskan panduan dan workflow untuk kontributor.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing Guidelines](#testing-guidelines)
- [Pull Request Process](#pull-request-process)

---

## Code of Conduct

Proyek ini mengikuti prinsip-prinsip:
- **Respect** - Hormati semua kontributor
- **Collaboration** - Kerja sama untuk hasil terbaik
- **Quality** - Utamakan kualitas kode

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL 8.0+ atau SQLite (development)

### Setup Development Environment

```bash
# Clone repository
git clone https://github.com/yourusername/siakad.git
cd siakad

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create SQLite database for development
touch database/database.sqlite

# Run migrations with seed data
php artisan migrate --seed

# Start development server
composer dev
```

Ini akan menjalankan:
- Laravel server (http://localhost:8000)
- Vite dev server (hot reload)
- Queue worker
- Log viewer

---

## Development Workflow

### Branch Naming Convention

```
feature/nama-fitur     # Fitur baru
bugfix/nama-bug        # Perbaikan bug
hotfix/nama-hotfix     # Perbaikan urgent production
refactor/nama-area     # Refactoring
docs/nama-dokumen      # Dokumentasi
```

### Commit Message Format

```
<type>(<scope>): <description>

[optional body]
```

**Types:**
- `feat`: Fitur baru
- `fix`: Bug fix
- `docs`: Dokumentasi
- `style`: Formatting (tidak mengubah logic)
- `refactor`: Refactoring code
- `test`: Menambah/memperbaiki tests
- `chore`: Maintenance

**Examples:**
```bash
feat(krs): add bulk approve feature for admin
fix(presensi): fix incorrect attendance calculation
docs(readme): update installation instructions
test(auth): add password reset tests
```

---

## Coding Standards

### PHP / Laravel

Kami menggunakan **Laravel Pint** untuk code formatting:

```bash
# Check code style
./vendor/bin/pint --test

# Auto-fix code style
./vendor/bin/pint
```

### Key Conventions

```php
// ✅ Use dependency injection
public function __construct(
    private readonly KrsService $krsService
) {}

// ✅ Use Form Requests for validation
public function store(StoreKrsRequest $request) {}

// ✅ Use Eloquent relationships with eager loading
$mahasiswa = Mahasiswa::with(['prodi', 'krs.krsDetail'])->find($id);

// ✅ Use services for business logic
class KrsService {
    public function calculateMaxSks(Mahasiswa $mahasiswa): int {}
}

// ❌ Avoid N+1 queries
foreach ($mahasiswa as $m) {
    echo $m->prodi->nama; // N+1 problem!
}

// ✅ Eager load instead
$mahasiswa = Mahasiswa::with('prodi')->get();
```

### Blade Templates

```blade
{{-- ✅ Use components --}}
<x-alert type="success">Berhasil!</x-alert>

{{-- ✅ Escape output by default --}}
{{ $user->name }}

{{-- ⚠️ Only use raw when absolutely necessary --}}
{!! $trustedHtml !!}
```

---

## Testing Guidelines

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Krs/KrsFlowTest.php

# Run tests in parallel
php artisan test --parallel

# Run with coverage
php artisan test --coverage --min=60
```

### Writing Tests

```php
// tests/Feature/Krs/KrsFlowTest.php

use App\Models\User;
use App\Models\Mahasiswa;

it('mahasiswa can view krs page', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'mahasiswa']);
    Mahasiswa::factory()->create(['user_id' => $user->id]);

    // Act
    $response = $this->actingAs($user)->get('/mahasiswa/krs');

    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('mahasiswa.krs.index');
});

it('validates sks limit when adding course', function () {
    // Test validation logic
});
```

### Test Categories

| Category | Location | Purpose |
|----------|----------|---------|
| Feature | `tests/Feature/` | HTTP tests, integration |
| Unit | `tests/Unit/` | Service/helper unit tests |
| Browser | (optional) | Laravel Dusk for E2E |

---

## Pull Request Process

### Before Submitting

- [ ] Tests pass: `php artisan test`
- [ ] Code style fixed: `./vendor/bin/pint`
- [ ] Documentation updated (if needed)
- [ ] Migrations are reversible

### PR Template

```markdown
## Description
[What does this PR do?]

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation

## Testing
- [ ] Tests added/updated
- [ ] Manual testing done

## Screenshots (if UI changes)
[Add screenshots here]

## Checklist
- [ ] Code follows project style
- [ ] Self-reviewed the code
- [ ] Tests pass locally
```

### Review Process

1. Create PR to `develop` branch
2. Wait for CI checks to pass
3. Request review from maintainers
4. Address feedback
5. Maintainer merges when approved

---

## Project Structure

```
siakad/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Thin controllers
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form validation
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic
│   ├── Repositories/         # Data access (optional)
│   └── DTOs/                 # Data transfer objects
├── config/
│   └── siakad.php           # App-specific config
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # DB migrations
│   └── seeders/             # Seed data
├── resources/
│   └── views/               # Blade templates
├── routes/
│   ├── web.php              # Web routes
│   └── auth.php             # Auth routes
├── tests/
│   ├── Feature/             # Feature tests
│   └── Unit/                # Unit tests
└── docs/                    # Documentation
```

---

## Need Help?

- 📝 Open an issue for bugs/features
- 💬 Start a discussion for questions
- 📧 Email: maintainer@example.com

**Happy contributing! 🎉**
