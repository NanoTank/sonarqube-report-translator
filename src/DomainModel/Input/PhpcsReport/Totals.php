<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpcsReport;

/**
 * @codeCoverageIgnore
 */
class Totals
{
    public function __construct(
        private readonly int $errors,
        private readonly int $warnings,
        private readonly int $fixable,
    ) {}

    public function getErrors(): int
    {
        return $this->errors;
    }

    public function getWarnings(): int
    {
        return $this->warnings;
    }

    public function getFixable(): int
    {
        return $this->fixable;
    }

}