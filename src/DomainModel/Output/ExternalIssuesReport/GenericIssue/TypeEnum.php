<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;

/**
 * @codeCoverageIgnore
 */
enum TypeEnum: string
{
    case Bug = 'BUG';
    case Vulnerability = 'VULNERABILITY';
    case CodeSmell = 'CODE_SMELL';
}
