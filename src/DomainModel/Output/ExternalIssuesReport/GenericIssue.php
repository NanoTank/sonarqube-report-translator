<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SecondaryLocationCollection;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum;

/**
 * @codeCoverageIgnore
 */
class GenericIssue
{
    public function __construct(
        private readonly string $engineId,
        private readonly string $ruleId,
        private readonly SeverityEnum $severity,
        private readonly TypeEnum $type,
        private readonly Location $primaryLocation,
        private readonly int $effortMinutes = 0,
        private readonly SecondaryLocationCollection $secondaryLocations = new SecondaryLocationCollection(),
    ) {
    }

    public function getEngineId(): string
    {
        return $this->engineId;
    }

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function getSeverity(): SeverityEnum
    {
        return $this->severity;
    }

    public function getType(): TypeEnum
    {
        return $this->type;
    }

    public function getPrimaryLocation(): Location
    {
        return $this->primaryLocation;
    }

    public function getEffortMinutes(): int
    {
        return $this->effortMinutes;
    }

    public function getSecondaryLocations(): SecondaryLocationCollection
    {
        return $this->secondaryLocations;
    }
}
