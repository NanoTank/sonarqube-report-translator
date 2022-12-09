<?php
declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\Transformer;

use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Input\PhpcsReport\File\Message\TypeEnum as PhpcsTypeEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum as GenericIssueTypeEnum;
use Powercloud\SRT\DomainModel\Transformer\PhpcsTransformer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhpcsTransformerTest extends KernelTestCase
{
    private PhpcsTransformer $testObject;

    public function setUp(): void
    {
        $this->testObject = new PhpcsTransformer();
    }

    public function testTransformCreatesValidReport(): void
    {
        $externalIssueReport = $this->testObject->transform(report: $report = $this->createPhpcsReport());

        $messages = [];
        $severities = [];
        $filePaths = [];
        foreach ($report->getFiles() as $file) {
            foreach ($file->getMessages() as $message) {
                $messages[] = $message;
                $severities[] = $message->getType();
                $filePaths[] = $file->getName();
            }
        }

        $this->assertCount(count($filePaths), $externalIssueReport->getGenericIssueCollection());
        foreach ($externalIssueReport->getGenericIssueCollection() as $issueKey => $issue) {
            $severity = match ($severities[$issueKey]) {
                PhpcsTypeEnum::Error => SeverityEnum::Major,
                PhpcsTypeEnum::Warning => SeverityEnum::Info,
            };

            $this->assertSame('PHPCS', $issue->getEngineId());
            $this->assertSame($messages[$issueKey]->getSource(), $issue->getRuleId());
            $this->assertEquals($severity, $issue->getSeverity());
            $this->assertEquals(GenericIssueTypeEnum::CodeSmell, $issue->getType());

            $this->assertSame(
                $messages[$issueKey]->getMessage(),
                $issue->getPrimaryLocation()->getMessage()
            );
            $this->assertSame(
                $filePaths[$issueKey],
                $issue->getPrimaryLocation()->getFilePath()
            );
            $this->assertEquals(
                $messages[$issueKey]->getLine(),
                $issue->getPrimaryLocation()->getTextRange()->getStartLine()
            );
            $this->assertEquals(
                $messages[$issueKey]->getColumn(),
                $issue->getPrimaryLocation()->getTextRange()->getStartColumn()
            );
        }
    }


    private function createPhpcsReport(): PhpcsReport
    {
        $file1 = new PhpcsReport\File(
            name: 'path/to/test/file1.php',
            errors: 1,
            warnings: 1,
            messages: [
                new PhpcsReport\File\Message(
                    message: 'TestMessage 1 of file 1',
                    source: 'Any.RuleOf.PHPCS.1',
                    severity: 1,
                    fixable: true,
                    type: PhpcsTypeEnum::Error,
                    line: 10,
                    column: 25
                ),
                new PhpcsReport\File\Message(
                    message: 'TestMessage 2 of file 1',
                    source: 'Any.RuleOf.PHPCS.2',
                    severity: 2,
                    fixable: true,
                    type: PhpcsTypeEnum::Warning,
                    line: 254,
                    column: 8
                ),
            ]
        );

        $file2 = new PhpcsReport\File(
            name: 'path/to/test/file2.php',
            errors: 2,
            warnings: 0,
            messages: [
                new PhpcsReport\File\Message(
                    message: 'TestMessage 1 of file 2',
                    source: 'Any.RuleOf.PHPCS.3',
                    severity: 10,
                    fixable: true,
                    type: PhpcsTypeEnum::Error,
                    line: 10,
                    column: 25
                ),
                new PhpcsReport\File\Message(
                    message: 'TestMessage 2 of file 2',
                    source: 'Any.RuleOf.PHPCS.4',
                    severity: 11,
                    fixable: false,
                    type: PhpcsTypeEnum::Error,
                    line: 254,
                    column: 8
                ),
            ]
        );

        $totals = new PhpcsReport\Totals(
            errors: 3,
            warnings: 1,
            fixable: 3
        );

        return new PhpcsReport(
            totals: $totals,
            files: [$file1, $file2]
        );
    }
}
