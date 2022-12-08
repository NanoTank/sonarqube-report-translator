<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input;

/**
 * @codeCoverageIgnore
 */
class DeptracReport
{
    public function __construct(
        private readonly int $violations,
        private readonly int $skippedViolations,
        private readonly int $uncovered,
        private readonly int $allowed,
        private readonly int $warnings,
        private readonly int $errors,
    ) {
    }

    public function getViolations(): int
    {
        return $this->violations;
    }

    public function getSkippedViolations(): int
    {
        return $this->skippedViolations;
    }

    public function getUncovered(): int
    {
        return $this->uncovered;
    }

    public function getAllowed(): int
    {
        return $this->allowed;
    }

    public function getWarnings(): int
    {
        return $this->warnings;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }
}
