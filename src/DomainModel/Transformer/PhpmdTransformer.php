<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Transformer;

use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location\TextRange;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum;

class PhpmdTransformer implements TransformerInterface
{
    /**
     * @param PhpmdReport $report
     * @return ExternalIssuesReport
     */
    public function transform(ReportInterface $report): ExternalIssuesReport
    {
        $externalIssues = [];

        foreach ($report->getFiles() as $file) {
            foreach ($file->getViolations() as $violation) {
                $textRange = new TextRange(
                    startLine: $violation->getBeginLine(),
                    endLine: $violation->getEndLine()
                );
                $location = new Location(
                    message: sprintf(
                        'Description: %s | URL: %s',
                        $violation->getDescription(),
                        $violation->getExternalInfoUrl()
                    ),
                    filePath: $file->getFile(),
                    textRange: $textRange
                );
                $externalIssues[] = new GenericIssue(
                    engineId: 'PHPMD',
                    ruleId: $violation->getRule(),
                    severity: SeverityEnum::Major,
                    type: TypeEnum::CodeSmell,
                    primaryLocation: $location
                );
            }
        }

        return new ExternalIssuesReport($externalIssues);
    }
}