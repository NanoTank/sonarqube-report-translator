<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssueCollection;

class ExternalIssuesReport
{
    public function __construct(
        private readonly GenericIssueCollection $genericIssueCollection
    ) {}

    public function getGenericIssueCollection(): GenericIssueCollection
    {
        return $this->genericIssueCollection;
    }
}
