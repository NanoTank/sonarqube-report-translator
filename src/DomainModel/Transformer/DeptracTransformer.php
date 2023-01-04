<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Transformer;

use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Input\DeptracReport\File\Message\TypeEnum;
use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location\TextRange;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum as GenericIssueTypeEnum;
use Powercloud\SRT\Exception\UnsupportedReportForTransformer;

class DeptracTransformer implements TransformerInterface
{
    public function transform(
        ReportInterface $report,
        TransformerOptions $transformerOptions = new TransformerOptions(),
    ): ExternalIssuesReport {
        if (false === $report instanceof DeptracReport) {
            throw new UnsupportedReportForTransformer(
                sprintf(
                    'Unsupported report of type [%s], expected [%s]',
                    get_debug_type($report),
                    DeptracReport::class,
                )
            );
        }
        $externalIssues = [];

        foreach ($report->getFiles() as $file) {
            foreach ($file->getMessages() as $message) {
                $severity = match ($message->getType()) {
                    TypeEnum::Error => SeverityEnum::Major,
                    TypeEnum::Warning => SeverityEnum::Minor,
                };
                $location = new Location(
                    message: $message->getMessage(),
                    filePath: $file->getPath(),
                    textRange: new TextRange(startLine: $message->getLine())
                );
                $externalIssues[] = new ExternalIssuesReport\Issue(
                    engineId: 'DEPTRAC',
                    ruleId: 'forbiddenDependency',
                    severity: $transformerOptions->getDefaultSeverity() ?: $severity,
                    type: $transformerOptions->getDefaultType() ?: GenericIssueTypeEnum::CodeSmell,
                    primaryLocation: $location,
                );
            }
        }

        return new ExternalIssuesReport($externalIssues);
    }

    public function supports(ReportInterface $report): bool
    {
        return $report instanceof DeptracReport;
    }
}
