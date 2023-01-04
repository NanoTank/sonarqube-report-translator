<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\Transformer;

use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum;
use Powercloud\SRT\DomainModel\Transformer\PhpmdTransformer;
use Powercloud\SRT\Exception\UnsupportedReportForTransformer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhpmdTransformerTest extends KernelTestCase
{
    private PhpmdTransformer $testObject;

    public function setUp(): void
    {
        $this->testObject = new PhpmdTransformer();
    }

    public function testSupportsCorrectClasses(): void
    {
        $supportedReport = $this->createMock(PhpmdReport::class);
        $unsupportedReport = new class implements ReportInterface{
        };

        $this->assertTrue($this->testObject->supports($supportedReport));
        $this->assertFalse($this->testObject->supports($unsupportedReport));
    }

    public function testTransformThrowsExceptionWhenUnsupportedReportType(): void
    {
        $report = new class implements ReportInterface {
        };

        $this->expectException(UnsupportedReportForTransformer::class);
        $this->expectExceptionMessage(
            sprintf(
                'Unsupported report of type [%s], expected [%s]',
                get_debug_type($report),
                PhpmdReport::class,
            )
        );
        $this->testObject->transform(report: $report);

    }

    public function testTransformCreatesValidReport(): void
    {
        $externalIssueReport = $this->testObject->transform(report: $report = $this->createPhpmdReport());

        $violations = [];
        $filePaths = [];
        foreach ($report->getFiles() as $file) {
            foreach ($file->getViolations() as $violation) {
                $violations[] = $violation;
                $filePaths[] = $file->getFile();
            }
        }

        foreach ($externalIssueReport->getIssues() as $issueKey => $issue) {
            $this->assertSame('PHPMD', $issue->getEngineId());
            $this->assertSame($violations[$issueKey]->getRule(), $issue->getRuleId());
            $this->assertEquals(SeverityEnum::Major, $issue->getSeverity());
            $this->assertEquals(TypeEnum::CodeSmell, $issue->getType());
            $this->assertEquals($filePaths[$issueKey], $issue->getPrimaryLocation()->getFilePath());

            $this->assertEquals(
                sprintf(
                    'Description: %s | URL: %s',
                    $violations[$issueKey]->getDescription(),
                    $violations[$issueKey]->getExternalInfoUrl()
                ),
                $issue->getPrimaryLocation()->getMessage()
            );
            $this->assertEquals(
                $violations[$issueKey]->getBeginLine(),
                $issue->getPrimaryLocation()->getTextRange()->getStartLine()
            );
            $this->assertEquals(
                $violations[$issueKey]->getEndLine(),
                $issue->getPrimaryLocation()->getTextRange()->getEndLine()
            );
        }
    }

    private function createPhpmdReport(): PhpmdReport
    {
        $file1 = new PhpmdReport\File(
            file: 'path/to/test/file1.php',
            violations: [
                new PhpmdReport\File\Violation(
                    beginLine: 10,
                    endLine: 10,
                    package: null,
                    function: null,
                    class: null,
                    method: null,
                    description: 'Avoid unused private fields such as \u0027$r\u0027.',
                    rule: 'UnusedPrivateField',
                    ruleSet: 'Unused Code Rules',
                    externalInfoUrl: 'https://phpmd.org/rules/unusedcode.html#unusedprivatefield',
                    priority: 3
                ),
                new PhpmdReport\File\Violation(
                    beginLine: 25,
                    endLine: 25,
                    package: null,
                    function: null,
                    class: null,
                    method: null,
                    description: 'Avoid variables with short names like $r. Configured minimum length is 3.',
                    rule:'ShortVariable',
                    ruleSet: 'Naming Rules',
                    externalInfoUrl: 'https://phpmd.org/rules/naming.html#shortvariable',
                    priority: 3
                ),
            ]
        );

        $file2 = new PhpmdReport\File(
            file: 'path/to/test/file2.php',
            violations: [
                new PhpmdReport\File\Violation(
                    beginLine: 101,
                    endLine: 101,
                    package: null,
                    function: null,
                    class: null,
                    method: null,
                    description: 'Avoid unused private fields such as \u0027$test\u0027.',
                    rule: 'UnusedPrivateField',
                    ruleSet: 'Unused Code Rules',
                    externalInfoUrl: 'https://phpmd.org/rules/unusedcode.html#unusedprivatefield',
                    priority: 3
                ),
            ]
        );

        return new PhpmdReport(
            version: '@package_version@',
            package: 'phpmd',
            timestamp: '2022-12-07T23:21:57+00:00',
            files: [$file1, $file2]
        );
    }
}
