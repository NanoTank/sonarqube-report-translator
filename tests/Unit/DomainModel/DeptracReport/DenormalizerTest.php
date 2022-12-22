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
            message: $data['files']['path/to/source/file1.php']['messages'][0]['message'],
            line: $data['files']['path/to/source/file1.php']['messages'][0]['line'],
            type: DeptracReport\File\Message\TypeEnum::Error
        );

        $deptracFile = new DeptracReport\File(
            violations: $data['files']['path/to/source/file1.php']['violations'],
            messages: [$deptracMessage],
            path: 'path/to/source/file1.php'
        );

        $deptracReport = new DeptracReport\Report(
            violations: $data['Report']['Violations'],
            skippedViolations: $data['Report']['Skipped violations'],
            uncovered: $data['Report']['Uncovered'],
            allowed: $data['Report']['Allowed'],
            warnings: $data['Report']['Warnings'],
            errors: $data['Report']['Errors']
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

        $this->assertSame($data['Report']['Violations'], $result->getReport()->getViolations());
        $this->assertSame($data['Report']['Skipped violations'], $result->getReport()->getSkippedViolations());
        $this->assertSame($data['Report']['Uncovered'], $result->getReport()->getUncovered());
        $this->assertSame($data['Report']['Allowed'], $result->getReport()->getAllowed());
        $this->assertSame($data['Report']['Warnings'], $result->getReport()->getWarnings());
        $this->assertSame($data['Report']['Errors'], $result->getReport()->getErrors());

        $this->assertCount(1, $result->getFiles());
        $this->assertSame(
            $data['files']['path/to/source/file1.php']['violations'],
            $result->getFiles()[0]->getViolations()
        );
        $this->assertSame('path/to/source/file1.php', $result->getFiles()[0]->getPath());

        $this->assertCount(1, $result->getFiles()[0]->getMessages());
        $this->assertSame(
            $data['files']['path/to/source/file1.php']['messages'][0]['message'],
            $result->getFiles()[0]->getMessages()[0]->getMessage()
        );
        $this->assertSame(
            $data['files']['path/to/source/file1.php']['messages'][0]['line'],
            $result->getFiles()[0]->getMessages()[0]->getLine());
        $this->assertSame(
            DeptracReport\File\Message\TypeEnum::Error,
            $result->getFiles()[0]->getMessages()[0]->getType()
        );
    }

    private function reportDataProvider(): array
    {
        return [
            [
                [
                    'Report' => [
                        'Violations' => 251,
                        'Skipped violations' => 0,
                        'Uncovered' => 802,
                        'Allowed' => 19456,
                        'Warnings' => 0,
                        'Errors' => 1,
                    ],
                    'files' => [
                        'path/to/source/file1.php' => [
                            'messages' => [
                                [
                                    'message' => 'Namespace\Path\To\Ernie must not depend on Namespace\Path\To\Bert',
                                    'line' => 16,
                                    'type' => 'error',
                                ],
                            ],
                            'violations' => 1,
                        ],
                    ],
                ]
            ],
        ];
    }
}
