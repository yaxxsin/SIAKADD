<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

/**
 * KRS Suggestion DTO
 * 
 * Contains intelligent KRS recommendations for a student.
 */
readonly class KrsSuggestion
{
    public function __construct(
        public Collection $priorityCourses,
        public Collection $optionalCourses,
        public array $warnings,
        public int $remainingSks,
        public int $maxSks
    ) {
    }

    /**
     * Check if there are any suggestions
     */
    public function hasSuggestions(): bool
    {
        return $this->priorityCourses->isNotEmpty() || $this->optionalCourses->isNotEmpty();
    }

    /**
     * Check if there are warnings
     */
    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
    }

    /**
     * Get total suggested SKS
     */
    public function totalSuggestedSks(): int
    {
        return $this->priorityCourses->sum('sks') + $this->optionalCourses->sum('sks');
    }

    /**
     * Check if student can add more courses
     */
    public function canAddMore(): bool
    {
        return $this->remainingSks > 0;
    }

    /**
     * Get warning by type
     */
    public function getWarning(string $type): ?array
    {
        foreach ($this->warnings as $warning) {
            if ($warning['type'] === $type) {
                return $warning;
            }
        }
        return null;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'priority_courses' => $this->priorityCourses->toArray(),
            'optional_courses' => $this->optionalCourses->toArray(),
            'warnings' => $this->warnings,
            'remaining_sks' => $this->remainingSks,
            'max_sks' => $this->maxSks,
            'total_suggested_sks' => $this->totalSuggestedSks(),
        ];
    }
}
