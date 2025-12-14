<?php

namespace App\Domain\Curriculum;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Cache;

/**
 * Curriculum Repository
 * 
 * Manages retrieval and caching of curriculum versions.
 * Determines which curriculum version applies to a student.
 */
class CurriculumRepository
{
    protected const CACHE_TTL = 3600; // 1 hour

    /**
     * Get curriculum for a student based on their enrollment year
     */
    public function getForStudent(Mahasiswa $mahasiswa): CurriculumVersion
    {
        $prodiKey = $this->getProdiKey($mahasiswa);
        $curriculumYear = $this->determineCurriculumYear($mahasiswa);

        return $this->get($prodiKey, $curriculumYear);
    }

    /**
     * Get specific curriculum version
     */
    public function get(string $prodiKey, int $year): CurriculumVersion
    {
        $cacheKey = "curriculum.{$prodiKey}.{$year}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($prodiKey, $year) {
            return new CurriculumVersion($prodiKey, $year);
        });
    }

    /**
     * Get all available curriculum versions for a prodi
     */
    public function getAvailableVersions(string $prodiKey): array
    {
        // Check config for available versions
        $versions = config("academic_rules.curriculum_versions.{$prodiKey}", []);

        if (empty($versions)) {
            // Default to current year if no versions defined
            $versions = [(int) date('Y')];
        }

        return $versions;
    }

    /**
     * Determine which curriculum year applies to a student
     */
    protected function determineCurriculumYear(Mahasiswa $mahasiswa): int
    {
        $enrollmentYear = (int) $mahasiswa->angkatan;
        $prodiKey = $this->getProdiKey($mahasiswa);

        $availableVersions = $this->getAvailableVersions($prodiKey);

        // Find the most recent curriculum version that's <= enrollment year
        $applicableVersions = array_filter($availableVersions, fn($v) => $v <= $enrollmentYear);

        if (empty($applicableVersions)) {
            // Student enrolled before any defined curriculum, use oldest
            return min($availableVersions);
        }

        return max($applicableVersions);
    }

    /**
     * Get prodi key for config lookup
     */
    protected function getProdiKey(Mahasiswa $mahasiswa): string
    {
        $prodiName = strtolower($mahasiswa->prodi->nama ?? '');

        // Map prodi names to config keys
        if (str_contains($prodiName, 'sistem informasi')) {
            return 'sistem_informasi_unri';
        }

        if (str_contains($prodiName, 'teknik informatika')) {
            return 'teknik_informatika';
        }

        if (str_contains($prodiName, 'ilmu komputer')) {
            return 'ilmu_komputer';
        }

        return 'default';
    }

    /**
     * Clear curriculum cache
     */
    public function clearCache(?string $prodiKey = null): void
    {
        if ($prodiKey) {
            $versions = $this->getAvailableVersions($prodiKey);
            foreach ($versions as $year) {
                Cache::forget("curriculum.{$prodiKey}.{$year}");
            }
        } else {
            // Clear all curriculum caches (pattern-based)
            // Note: This requires a cache driver that supports tags or pattern deletion
            Cache::flush();
        }
    }

    /**
     * Compare student progress against their curriculum
     */
    public function compareProgress(Mahasiswa $mahasiswa, array $completedCourseCodes): array
    {
        $curriculum = $this->getForStudent($mahasiswa);
        return $curriculum->calculateProgress($completedCourseCodes);
    }
}
