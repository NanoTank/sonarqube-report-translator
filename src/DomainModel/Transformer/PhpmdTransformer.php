<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Transformer;

use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\Issue;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location\TextRange;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum;
use Powercloud\SRT\Exception\UnsupportedReportForTransformer;

class PhpmdTransformer implements TransformerInterface
{
    public function transform(
        ReportInterface $report,
        TransformerOptions $transformerOptions = new TransformerOptions(),
    ): ExternalIssuesReport {
        if (false === $report instanceof PhpmdReport) {
            throw new UnsupportedReportForTransformer(
                sprintf(
                    'Unsupported report of type [%s], expected [%s]',
                    get_debug_type($report),
                    PhpmdReport::class,
                )
            );
        }

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
                        $violation->getExternalInfoUrl(),
                    ),
                    filePath: $file->getFile(),
                    textRange: $textRange
                );
                $externalIssues[] = new Issue(
                    engineId: 'PHPMD',
                    ruleId: $violation->getRule(),
                    severity: $transformerOptions->getDefaultSeverity() ?: SeverityEnum::Major,
                    type: $transformerOptions->getDefaultType() ?: TypeEnum::CodeSmell,
                    primaryLocation: $location
                );
            }
        }

        return new ExternalIssuesReport($externalIssues);
    }

    public function supports(ReportInterface $report): bool
    {
        return $report instanceof PhpmdReport;
    }
}
