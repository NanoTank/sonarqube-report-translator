<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Transformer;

use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Input\PhpcsReport\File\Message\TypeEnum;
use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum as GenericIssueTypeEnum;

class PhpcsTransformer implements TransformerInterface
{
    /**
     * @param PhpcsReport $report
     * @param TransformerOptions $transformerOptions
     *
     * @return ExternalIssuesReport
     */
    public function transform(
        ReportInterface $report,
        TransformerOptions $transformerOptions = new TransformerOptions(),
    ): ExternalIssuesReport {
        $externalIssues = [];

        foreach ($report->getFiles() as $file) {
            foreach ($file->getMessages() as $message) {
                $severity = match ($message->getType()) {
                    TypeEnum::Error => SeverityEnum::Major,
                    TypeEnum::Warning => SeverityEnum::Info,
                };
                $textRange = new Location\TextRange(
                    startLine: $message->getLine(),
                    startColumn: $message->getColumn()
                );
                $location = new Location(
                    message: $message->getMessage(),
                    filePath: $file->getPath(),
                    textRange: $textRange
                );
                $externalIssues[] = new ExternalIssuesReport\GenericIssue(
                    engineId: 'PHPCS',
                    ruleId: $message->getSource(),
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
        return $report instanceof PhpcsReport;
    }
}
