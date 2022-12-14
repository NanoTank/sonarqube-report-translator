<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Transformer;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;

class TransformerOptions
{
    public function __construct(
        private readonly ?ExternalIssuesReport\GenericIssue\SeverityEnum $defaultSeverity = null,
        private readonly ?ExternalIssuesReport\GenericIssue\TypeEnum $defaultType = null,
    ) {
    }

    public function getDefaultSeverity(): ?ExternalIssuesReport\GenericIssue\SeverityEnum
    {
        return $this->defaultSeverity;
    }

    public function getDefaultType(): ?ExternalIssuesReport\GenericIssue\TypeEnum
    {
        return $this->defaultType;
    }
}