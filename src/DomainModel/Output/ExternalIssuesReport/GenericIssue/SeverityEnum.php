<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;

/**
 * @codeCoverageIgnore
 */
enum SeverityEnum: string
{
    case Blocker = 'BLOCKER';
    case Critical = 'CRITICAL';
    case Major = 'MAJOR';
    case Minor  = 'MINOR';
    case Info = 'INFO';
}
