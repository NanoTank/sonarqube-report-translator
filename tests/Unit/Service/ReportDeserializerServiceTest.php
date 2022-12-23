<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\Service;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Powercloud\SRT\Exception\UnsupportedReportException;
use Powercloud\SRT\Service\ReportDeserializerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ReportDeserializerServiceTest extends TestCase
{
    private SerializerInterface | MockObject $serializer;
    private LoggerInterface | MockObject $logger;
    private ReportDeserializerService $testObject;

    public function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->testObject = new ReportDeserializerService(
            $this->serializer,
            $this->logger
        );
    }

    public function testDeserializationThrowsExceptionIfUnsupportedFormatIsProvided(): void
    {
        $unsupportedReport = '{"unsupported": {"weird": 4,"stuff": 100}}';

        $this->expectException(UnsupportedReportException::class);
        $this->expectExceptionMessageMatches(
            '/Failed to deserialize report, format not supported. Supported formats are:/'
        );

        $this->testObject->deserialize($unsupportedReport);
    }

    /**
     * @dataProvider reportProvider
     */
    public function testDeserializationLogsExceptions(string $report): void
    {
        $this->logger
            ->expects(self::atLeastOnce())
            ->method('info')
            ->with(
                'Attempted to deserialize report into a [json] format, but failed with message: Test Exception Message'
            );

        $this->serializer
            ->expects(self::atLeastOnce())
            ->method('deserialize')
            ->willThrowException(new Exception('Test Exception Message'));

        $this->expectException(UnsupportedReportException::class);

        $this->testObject->deserialize($report);
    }

    /**
     * @dataProvider reportProvider
     */
    public function testDeserializationWorksCorrectly(
        string $validReport,
        string $expectedFormat
    ): void {
        $this->serializer
            ->expects(self::atLeastOnce())
            ->method('deserialize')
            ->willReturn($this->createMock($expectedFormat));

        $this->assertInstanceOf(
            $expectedFormat,
            $this->testObject->deserialize($validReport)
        );
    }

    private function reportProvider(): array
    {
        return [
            [file_get_contents(__DIR__ . '/../../TestFiles/phpcs.json'), PhpcsReport::class],
            [file_get_contents(__DIR__ . '/../../TestFiles/phpmd.json'), PhpmdReport::class],
            [file_get_contents(__DIR__ . '/../../TestFiles/deptrac.json'), DeptracReport::class],
        ];
    }
}
