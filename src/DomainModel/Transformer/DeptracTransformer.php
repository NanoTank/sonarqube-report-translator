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

class DeptracTransformer implements TransformerInterface
{
    /**
     * @param DeptracReport $report
     * @return ExternalIssuesReport
     */
    public function transform(ReportInterface $report): ExternalIssuesReport
    {
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
                $externalIssues[] = new ExternalIssuesReport\GenericIssue(
                    engineId: 'DEPTRAC',
                    ruleId: 'Bad usage',
                    severity: $severity,
                    type: GenericIssueTypeEnum::CodeSmell,
                    primaryLocation: $location,
                );
            }
        }

        return new ExternalIssuesReport($externalIssues);
    }
}
