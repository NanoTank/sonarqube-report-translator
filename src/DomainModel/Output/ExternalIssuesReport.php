<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;

/**
 * @codeCoverageIgnore
 */
class ExternalIssuesReport
{
    public function __construct(
        /** @var GenericIssue[] $genericIssueCollection */
        private readonly array $genericIssueCollection
    ) {
    }

    /**
     * @return GenericIssue[]
     */
    public function getGenericIssueCollection(): array
    {
        return $this->genericIssueCollection;
    }
}
