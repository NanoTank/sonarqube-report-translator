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
    ) {}
}
