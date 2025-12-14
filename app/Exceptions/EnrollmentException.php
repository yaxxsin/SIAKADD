<?php

namespace App\Exceptions;

/**
 * Enrollment Exception
 * 
 * Thrown when enrollment (KRS) operations fail due to policy violations.
 */
class EnrollmentException extends AcademicException
{
    // Error codes
    public const ENROLLMENT_CLOSED = 'ENROLLMENT_CLOSED';
    public const SKS_LIMIT_EXCEEDED = 'SKS_LIMIT_EXCEEDED';
    public const CLASS_FULL = 'CLASS_FULL';
    public const DUPLICATE_COURSE = 'DUPLICATE_COURSE';
    public const PREREQUISITE_NOT_MET = 'PREREQUISITE_NOT_MET';
    public const KRS_LOCKED = 'KRS_LOCKED';
    public const KRS_EMPTY = 'KRS_EMPTY';
    public const SCHEDULE_CONFLICT = 'SCHEDULE_CONFLICT';

    public static function enrollmentClosed(): self
    {
        return new self(
            'Masa pengisian KRS sudah ditutup.',
            self::ENROLLMENT_CLOSED
        );
    }

    public static function sksLimitExceeded(int $current, int $max, int $adding): self
    {
        return new self(
            "Melebihi batas SKS ({$max}). Total akan menjadi: " . ($current + $adding),
            self::SKS_LIMIT_EXCEEDED,
            ['current' => $current, 'max' => $max, 'adding' => $adding]
        );
    }

    public static function classFull(string $className, int $capacity): self
    {
        return new self(
            "Kelas {$className} sudah penuh. Kapasitas: {$capacity}",
            self::CLASS_FULL,
            ['class' => $className, 'capacity' => $capacity]
        );
    }

    public static function duplicateCourse(string $courseName): self
    {
        return new self(
            "Mata kuliah {$courseName} sudah diambil.",
            self::DUPLICATE_COURSE,
            ['course' => $courseName]
        );
    }

    public static function prerequisiteNotMet(string $course, string $prerequisite): self
    {
        return new self(
            "Prasyarat belum terpenuhi: {$prerequisite} diperlukan untuk {$course}.",
            self::PREREQUISITE_NOT_MET,
            ['course' => $course, 'prerequisite' => $prerequisite]
        );
    }

    public static function krsLocked(string $status): self
    {
        return new self(
            "KRS tidak dapat diubah. Status: {$status}",
            self::KRS_LOCKED,
            ['status' => $status]
        );
    }

    public static function krsEmpty(): self
    {
        return new self(
            "KRS kosong tidak dapat diajukan.",
            self::KRS_EMPTY
        );
    }

    public static function scheduleConflict(string $course1, string $course2, string $time): self
    {
        return new self(
            "Jadwal bentrok: {$course1} dengan {$course2} pada {$time}",
            self::SCHEDULE_CONFLICT,
            ['course1' => $course1, 'course2' => $course2, 'time' => $time]
        );
    }
}
