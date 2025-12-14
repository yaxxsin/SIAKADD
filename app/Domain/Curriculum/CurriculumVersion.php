<?php

namespace App\Domain\Curriculum;

use Illuminate\Support\Collection;

/**
 * Curriculum Version
 * 
 * Represents a versioned curriculum for a program of study.
 * Supports multiple curriculum generations (e.g., 2019, 2023).
 */
class CurriculumVersion
{
    protected string $prodiKey;
    protected int $year;
    protected array $semesters = [];
    protected array $rules = [];

    public function __construct(string $prodiKey, int $year)
    {
        $this->prodiKey = $prodiKey;
        $this->year = $year;
        $this->loadFromConfig();
    }

    /**
     * Load curriculum from config
     */
    protected function loadFromConfig(): void
    {
        // Try versioned config first
        $versionedKey = "academic_rules.kurikulum.{$this->prodiKey}_{$this->year}";
        $curriculum = config($versionedKey);

        // Fall back to non-versioned
        if (!$curriculum) {
            $curriculum = config("academic_rules.kurikulum.{$this->prodiKey}", []);
        }

        $this->semesters = $curriculum;

        // Load rules
        $rulesKey = "academic_rules.prodi.{$this->prodiKey}";
        $this->rules = config($rulesKey, config('academic_rules.default', []));
    }

    /**
     * Get all courses for a specific semester
     */
    public function getCoursesForSemester(int $semester): Collection
    {
        return collect($this->semesters[$semester] ?? []);
    }

    /**
     * Get all semesters
     */
    public function getAllSemesters(): Collection
    {
        return collect($this->semesters)->map(function ($courses, $semester) {
            return [
                'semester' => $semester,
                'courses' => collect($courses),
                'total_sks' => collect($courses)->sum('sks'),
            ];
        })->values();
    }

    /**
     * Get all required courses
     */
    public function getRequiredCourses(): Collection
    {
        return collect($this->semesters)
            ->flatten(1)
            ->filter(fn($course) => ($course['wajib'] ?? true) === true);
    }

    /**
     * Get all elective courses
     */
    public function getElectiveCourses(): Collection
    {
        return collect($this->semesters)
            ->flatten(1)
            ->filter(fn($course) => ($course['wajib'] ?? true) === false);
    }

    /**
     * Find course by code
     */
    public function findCourse(string $kode): ?array
    {
        foreach ($this->semesters as $semester => $courses) {
            foreach ($courses as $course) {
                if ($course['kode'] === $kode) {
                    return array_merge($course, ['semester' => $semester]);
                }
            }
        }
        return null;
    }

    /**
     * Find course by name (fuzzy)
     */
    public function findCourseByName(string $name): ?array
    {
        $nameLower = strtolower($name);

        foreach ($this->semesters as $semester => $courses) {
            foreach ($courses as $course) {
                if (str_contains(strtolower($course['nama']), $nameLower)) {
                    return array_merge($course, ['semester' => $semester]);
                }
            }
        }
        return null;
    }

    /**
     * Get total SKS required for graduation
     */
    public function getTotalRequiredSks(): int
    {
        return $this->rules['graduation_total_sks'] ?? 144;
    }

    /**
     * Get minimum SKS for thesis
     */
    public function getMinSksForThesis(): int
    {
        return $this->rules['thesis_min_sks'] ?? 120;
    }

    /**
     * Get minimum SKS for internship
     */
    public function getMinSksForInternship(): int
    {
        return $this->rules['internship']['min_sks_required'] ?? 90;
    }

    /**
     * Get curriculum version year
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Get prodi key
     */
    public function getProdiKey(): string
    {
        return $this->prodiKey;
    }

    /**
     * Get graduation rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Calculate progress based on completed courses
     */
    public function calculateProgress(array $completedCodes): array
    {
        $totalCourses = 0;
        $completedCourses = 0;
        $totalSks = 0;
        $completedSks = 0;

        foreach ($this->semesters as $courses) {
            foreach ($courses as $course) {
                $totalCourses++;
                $totalSks += $course['sks'];

                if (in_array($course['kode'], $completedCodes)) {
                    $completedCourses++;
                    $completedSks += $course['sks'];
                }
            }
        }

        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'remaining_courses' => $totalCourses - $completedCourses,
            'total_sks' => $totalSks,
            'completed_sks' => $completedSks,
            'remaining_sks' => $totalSks - $completedSks,
            'completion_percentage' => $totalCourses > 0
                ? round(($completedCourses / $totalCourses) * 100, 1)
                : 0,
        ];
    }
}
