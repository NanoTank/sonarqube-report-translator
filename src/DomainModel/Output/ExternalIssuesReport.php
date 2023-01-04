<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\Issue;

/**
 * @codeCoverageIgnore
 */
class ExternalIssuesReport
{
    public function __construct(
        /** @var Issue[] $issues */
        private readonly array $issues
    ) {
    }

    /**
     * @return Issue[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }
}
