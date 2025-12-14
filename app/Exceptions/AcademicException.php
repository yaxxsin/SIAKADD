<?php

namespace App\Exceptions;

use Exception;

/**
 * Base Academic Exception
 * 
 * Parent class for all domain-specific academic exceptions.
 * Provides error codes and structured error handling.
 */
abstract class AcademicException extends Exception
{
    protected string $errorCode;
    protected array $context = [];

    public function __construct(
        string $message,
        string $errorCode,
        array $context = [],
        int $httpCode = 400
    ) {
        parent::__construct($message, $httpCode);
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Convert to API response format
     */
    public function toArray(): array
    {
        return [
            'error' => true,
            'code' => $this->errorCode,
            'message' => $this->getMessage(),
            'context' => $this->context,
        ];
    }
}
