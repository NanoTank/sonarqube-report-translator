<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\Transformer;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Input\DeptracReport\Report;
use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum;
use Powercloud\SRT\DomainModel\Input\DeptracReport\File\Message\TypeEnum as DeptracTypeEnum;
use Powercloud\SRT\DomainModel\Transformer\DeptracTransformer;

class DeptracTransformerTest extends TestCase
{
    private DeptracTransformer $testObject;

    public function setUp(): void
    {
        $this->testObject = new DeptracTransformer();
    }

    public function testSupportsCorrectClasses(): void
    {
        $supportedReport = $this->createMock(DeptracReport::class);
        $unsupportedReport = new class implements ReportInterface{};

        $this->assertTrue($this->testObject->supports($supportedReport));
        $this->assertFalse($this->testObject->supports($unsupportedReport));
    }

    public function testTransformCreatesValidReport(): void
    {
        $externalIssueReport = $this->testObject->transform(report: $report = $this->createDeptracReport());

        $filePaths = [];
        $messages = [];
        $severities = [];
        foreach ($report->getFiles() as $file) {
            foreach ($file->getMessages() as $message) {
                $filePaths[] = $file->getPath();
                $messages[] = $message;
                $severities[] = $message->getType();
            }
        }

        $this->assertCount(count($filePaths), $externalIssueReport->getGenericIssueCollection());
        foreach ($externalIssueReport->getGenericIssueCollection() as $issueKey => $issue) {
            $severity = match ($severities[$issueKey]) {
                DeptracTypeEnum::Error => SeverityEnum::Major,
                DeptracTypeEnum::Warning => SeverityEnum::Minor,
            };

            $this->assertSame('DEPTRAC', $issue->getEngineId());
            $this->assertSame('forbiddenDependency', $issue->getRuleId());
            $this->assertEquals($severity, $issue->getSeverity());
            $this->assertEquals(TypeEnum::CodeSmell, $issue->getType());

            $this->assertSame(
                $messages[$issueKey]->getMessage(),
                $issue->getPrimaryLocation()->getMessage()
            );
            $this->assertSame(
                $messages[$issueKey]->getLine(),
                $issue->getPrimaryLocation()->getTextRange()->getStartLine()
            );
            $this->assertSame(
                $filePaths[$issueKey],
                $issue->getPrimaryLocation()->getFilePath()
            );
        }
    }

    private function createDeptracReport(): DeptracReport
    {
        $report = new Report(
            violations: 5,
            skippedViolations: 2,
            uncovered: 120,
            allowed: 100,
            warnings: 2,
            errors: 2
        );

        $file1 = new DeptracReport\File(
            violations: 2,
            messages: [
                new DeptracReport\File\Message(
                    message: 'TestMessage 1 of file 1',
                    line: 10,
                    type: DeptracTypeEnum::Error
                ),
                new DeptracReport\File\Message(
                    message: 'TestMessage 2 of file 1',
                    line: 25,
                    type: DeptracTypeEnum::Warning
                ),
            ],
            path: 'path/to/test/file1.php'
        );

        $file2 = new DeptracReport\File(
            violations: 3,
            messages: [
                new DeptracReport\File\Message(
                    message: 'TestMessage 1 of file 2',
                    line: 105,
                    type: DeptracTypeEnum::Error
                ),
                new DeptracReport\File\Message(
                    message: 'TestMessage 2 of file 2',
                    line: 252,
                    type: DeptracTypeEnum::Warning
                ),
            ],
            path: 'path/to/test/file2.php'
        );

        return new DeptracReport(
            report: $report,
            files: [$file1, $file2]
        );
    }
}
