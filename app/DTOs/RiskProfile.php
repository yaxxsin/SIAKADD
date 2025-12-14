<?php

namespace App\DTOs;

/**
 * Risk Profile DTO
 * 
 * Represents a student's computed risk assessment.
 * Immutable data transfer object.
 */
readonly class RiskProfile
{
    public function __construct(
        public int $score,
        public string $level,
        public array $flags,
        public array $factors,
        public array $recommendations
    ) {
    }

    /**
     * Check if risk level is critical
     */
    public function isCritical(): bool
    {
        return $this->level === 'CRITICAL';
    }

    /**
     * Check if risk level is high or above
     */
    public function isHighRisk(): bool
    {
        return in_array($this->level, ['HIGH', 'CRITICAL']);
    }

    /**
     * Check if student has a specific flag
     */
    public function hasFlag(string $flag): bool
    {
        return in_array($flag, $this->flags);
    }

    /**
     * Get color class for UI display
     */
    public function getColorClass(): string
    {
        return match ($this->level) {
            'LOW' => 'text-emerald-600 bg-emerald-100',
            'MEDIUM' => 'text-amber-600 bg-amber-100',
            'HIGH' => 'text-orange-600 bg-orange-100',
            'CRITICAL' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }

    /**
     * Get icon for UI display
     */
    public function getIcon(): string
    {
        return match ($this->level) {
            'LOW' => '✓',
            'MEDIUM' => '⚠',
            'HIGH' => '⚡',
            'CRITICAL' => '🚨',
            default => '•',
        };
    }

    /**
     * Get Indonesian label
     */
    public function getLevelLabel(): string
    {
        return match ($this->level) {
            'LOW' => 'Risiko Rendah',
            'MEDIUM' => 'Perlu Perhatian',
            'HIGH' => 'Risiko Tinggi',
            'CRITICAL' => 'Kritis',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Convert to array for serialization
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'level' => $this->level,
            'level_label' => $this->getLevelLabel(),
            'flags' => $this->flags,
            'factors' => $this->factors,
            'recommendations' => $this->recommendations,
            'is_high_risk' => $this->isHighRisk(),
        ];
    }
}
