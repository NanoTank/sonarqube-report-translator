<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\DomainModel\Transformer\TransformerInterface;
use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Powercloud\SRT\Exception\InvalidParameterException;
use Powercloud\SRT\Exception\UnsupportedReportException;
use Powercloud\SRT\Exception\UnsupportedReportForTransformer;
use Powercloud\SRT\Service\ReportToSonarqubeConverterService;

class ReportToSonarqubeConverterServiceTest extends TestCase
{
    private TransformerOptions|MockObject $transformerOptions;
    private TransformerInterface|MockObject $transformer1;
    private TransformerInterface|MockObject $transformer2;

    public function setUp(): void
    {
        $this->transformer1 = $this->createMock(TransformerInterface::class);
        $this->transformer2 = $this->createMock(TransformerInterface::class);
        $this->transformer3 = $this->createMock(TransformerInterface::class);
        $this->transformerOptions = $this->createMock(TransformerOptions::class);
    }

    public function testConstructWithInvalidTransformers(): void
    {
        $invalidTransformers = [
            new class {
            },
            new class {
            },
        ];

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage(
            'Parameter of type [Powercloud\SRT\DomainModel\Transformer\TransformerInterface] expected, '
            . 'but [class@anonymous] received'
        );

        new ReportToSonarqubeConverterService($invalidTransformers);
    }

    /**
     * @dataProvider reportProvider
     */
    public function testTransformersIfSupportedButUnsuitable(
        ReportInterface $report
    ): void {
        $this->transformer1
            ->expects(self::once())
            ->method('supports')
            ->willReturn(false);
        $this->transformer1
            ->expects(self::never())
            ->method('transform');

        $this->transformer2
            ->expects(self::once())
            ->method('supports')
            ->willReturn(true);
        $this->transformer2
            ->expects(self::once())
            ->method('transform')
            ->willThrowException(new UnsupportedReportForTransformer());

        $testObject = new ReportToSonarqubeConverterService([
            $this->transformer1,
            $this->transformer2,
        ]);

        $this->expectException(UnsupportedReportException::class);

        $testObject->convert($report, $this->transformerOptions);
    }

    /**
     * @dataProvider reportProvider
     */
    public function testTransformersIfSupported(
        ReportInterface $report
    ): void {
        $externalIssuesReport = $this->createMock(ExternalIssuesReport::class);

        $this->transformer1
            ->expects(self::once())
            ->method('supports')
            ->willReturn(false);
        $this->transformer1
            ->expects(self::never())
            ->method('transform');

        $this->transformer2
            ->expects(self::once())
            ->method('supports')
            ->willReturn(true);
        $this->transformer2
            ->expects(self::once())
            ->method('transform')
            ->willReturn($externalIssuesReport);

        $testObject = new ReportToSonarqubeConverterService([
            $this->transformer1,
            $this->transformer2,
        ]);

        $testObject->convert(
            $report,
            $this->transformerOptions
        );
    }

    private function reportProvider(): array
    {
        $phpcsReportMessage = new PhpcsReport\File\Message(
            message: 'Test Message 1',
            source: '/path/to/source.php',
            severity: 11,
            fixable: true,
            type: PhpcsReport\File\Message\TypeEnum::Error,
            line: 12,
            column: 13
        );
        $phpcsReportFile = new PhpcsReport\File(
            errors: 1,
            warnings: 0,
            messages: [$phpcsReportMessage],
            path: '/path/to/phpcs/report/message'
        );
        $phpcsReportTotals = new PhpcsReport\Totals(
            errors: 1,
            warnings: 1,
            fixable: 1
        );
        $fullPhpcsReport = new PhpcsReport(
            totals: $phpcsReportTotals,
            files: [$phpcsReportFile]
        );

        $phpmdReportViolation = new PhpmdReport\File\Violation(
            beginLine: 1,
            endLine: 1,
            package: 'testpackage',
            function: 'testFunctionName',
            class: 'testClassName',
            method: 'testMethodName',
            description: 'Any Test Description',
            rule: 'testRule',
            ruleSet: 'test.rule.set',
            externalInfoUrl: '/external/url/to/rule?somewhere',
            priority: 1
        );
        $phpmdReportFile = new PhpmdReport\File(
            file: '/path/to/file.php',
            violations: [$phpmdReportViolation]
        );
        $fullPhpmdReport = new PhpmdReport(
            version: '',
            package: '',
            timestamp: '',
            files: [$phpmdReportFile]
        );

        $deptracMessage = new DeptracReport\File\Message(
            message: 'Test Message Deptrac Report',
            line: 1,
            type:DeptracReport\File\Message\TypeEnum::Error
        );
        $deptracFile = new DeptracReport\File(
            violations: 1,
            messages: [$deptracMessage],
            path: '/path/to/file.php'
        );
        $deptracReport = new DeptracReport\Report(
            violations: 1,
            skippedViolations: 0,
            uncovered: 0,
            allowed: 1,
            warnings: 0,
            errors: 1
        );
        $fullDeptracReport = new DeptracReport(
            report: $deptracReport,
            files: [$deptracFile]
        );

        return [
            [$fullPhpcsReport],
            [$fullPhpmdReport],
            [$fullDeptracReport],
        ];
    }
}
