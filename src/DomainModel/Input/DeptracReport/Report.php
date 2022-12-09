<?php

namespace Powercloud\SRT\DomainModel\Input\DeptracReport;

use Symfony\Component\Serializer\Annotation as Serializer;

class Report
{
    public function __construct(
        #[Serializer\SerializedName('Violations')]
        private readonly int $violations,
        #[Serializer\SerializedName('Skipped violations')]
        private readonly int $skippedViolations,
        #[Serializer\SerializedName('Uncovered')]
        private readonly int $uncovered,
        #[Serializer\SerializedName('Allowed')]
        private readonly int $allowed,
        #[Serializer\SerializedName('Warnings')]
        private readonly int $warnings,
        #[Serializer\SerializedName('Errors')]
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
