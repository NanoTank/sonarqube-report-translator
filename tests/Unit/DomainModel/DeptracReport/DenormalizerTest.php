<?php
declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\DomainModel\DeptracReport;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Input\DeptracReport\Denormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DenormalizerTest extends TestCase
{
    private Denormalizer $testObject;

    public function setUp(): void
    {
        $this->testObject = new Denormalizer();
    }

    public function testSupportsCorrectReportClass(): void
    {
        $this->assertTrue($this->testObject->supportsDenormalization([], DeptracReport::class));
        $this->assertFalse($this->testObject->supportsDenormalization([], \stdClass::class));
    }

    /**
     * @dataProvider reportDataProvider
     */
    public function testDenormalizeWorksCorrectly(array $data): void
    {
        $deptracMessage = new DeptracReport\File\Message(
            message: 'Test Message',
            line: 10,
            type: DeptracReport\File\Message\TypeEnum::Error
        );

        $deptracFile = new DeptracReport\File(
            violations: 1,
            messages: [],
            path: ''
        );

        $deptracReport = new DeptracReport\Report(
            violations: 1,
            skippedViolations: 3,
            uncovered: 0,
            allowed: 3,
            warnings: 4,
            errors: 1
        );

        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer
            ->expects(self::exactly(2))
            ->method('denormalize')
            ->willReturnCallback(
                function($data, $type) use ($deptracMessage, $deptracFile, $deptracReport) {
                    return match($type) {
                        DeptracReport\Report::class => $deptracReport,
                        DeptracReport\File::class => $deptracFile,
                        DeptracReport\File\Message::class => $deptracMessage,
                        default => new \stdClass()
                    };
                });

        $this->testObject->setDenormalizer($denormalizer);
        $result = $this->testObject->denormalize($data, DeptracReport::class);

        $this->assertSame(1, $result->getReport()->getViolations());
        $this->assertSame(3, $result->getReport()->getSkippedViolations());
        $this->assertSame(0, $result->getReport()->getUncovered());
        $this->assertSame(3, $result->getReport()->getAllowed());
        $this->assertSame(4, $result->getReport()->getWarnings());
        $this->assertSame(1, $result->getReport()->getErrors());

        $this->assertCount(1, $result->getFiles());
        $this->assertSame(1, $result->getFiles()[0]->getViolations());
        $this->assertSame('path/to/test/report/file', $result->getFiles()[0]->getPath());

        $this->assertCount(1, $result->getFiles()[0]->getMessages());
        $this->assertSame('Test Message', $result->getFiles()[0]->getMessages()[0]->getMessage());
        $this->assertSame(10, $result->getFiles()[0]->getMessages()[0]->getLine());
        $this->assertSame(DeptracReport\File\Message\TypeEnum::Error, $result->getFiles()[0]->getMessages()[0]->getType());
    }

    private function reportDataProvider(): array
    {
        return [
            [
                [
                    'Report' => [
                        'errors' => 1,
                        'warnings' => 0,
                        'fixable' => 1,
                    ],
                    'files' => [
                        'path/to/test/report/file' => [
                            'messages' => [
                                'TestMessage 1',
                            ],
                            'violations' => 1,
                        ],
                    ],
                ]
            ],
        ];
    }


}
