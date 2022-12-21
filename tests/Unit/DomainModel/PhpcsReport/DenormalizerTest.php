<?php
declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\DomainModel\PhpcsReport;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Input\PhpcsReport\Denormalizer;
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
        $this->assertTrue($this->testObject->supportsDenormalization([], PhpcsReport::class));
        $this->assertFalse($this->testObject->supportsDenormalization([], \stdClass::class));
    }

    public function testDenormalizeWithMissingTotalsInReportThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Missing [totals] in the phpcs report data');

        $this->testObject->denormalize([], PhpcsReport::class);
    }

    /**
     * @dataProvider reportDataProvider
     */
    public function testDenormalizeWorksCorrectly(array $data): void
    {
        $totals = new PhpcsReport\Totals(
            errors: 1,
            warnings: 0,
            fixable: 1
        );

        $message = new PhpcsReport\File\Message(
            message: 'Test Message 1',
            source: 'test source 1',
            severity: 1,
            fixable: true,
            type: PhpcsReport\File\Message\TypeEnum::Error,
            line: 10,
            column: 20
        );

        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer
            ->expects(self::exactly(2))
            ->method('denormalize')
            ->willReturnCallback(
                function($data, $type) use ($totals, $message) {
                    return match($type) {
                        PhpcsReport\Totals::class => $totals,
                        PhpcsReport\File\Message::class => $message,
                        default => new \stdClass()
                    };
            });

        $this->testObject->setDenormalizer($denormalizer);

        $result = $this->testObject->denormalize($data, PhpcsReport::class);

        $this->assertSame(1, $result->getTotals()->getErrors());
        $this->assertSame(0, $result->getTotals()->getWarnings());
        $this->assertSame(1, $result->getTotals()->getFixable());

        $this->assertSame(1, $result->getFiles()[0]->getErrors());
        $this->assertSame(0, $result->getFiles()[0]->getWarnings());

        $this->assertCount(1, $result->getFiles());
        $this->assertSame('path/to/test/report/file', $result->getFiles()[0]->getPath());

        $this->assertCount(1, $result->getFiles()[0]->getMessages());
        $this->assertSame(PhpcsReport\File\Message\TypeEnum::Error, $result->getFiles()[0]->getMessages()[0]->getType());
        $this->assertSame(1, $result->getFiles()[0]->getMessages()[0]->getSeverity());
        $this->assertSame(10, $result->getFiles()[0]->getMessages()[0]->getLine());
        $this->assertSame(20, $result->getFiles()[0]->getMessages()[0]->getColumn());
        $this->assertSame('test source 1', $result->getFiles()[0]->getMessages()[0]->getSource());
        $this->assertSame('Test Message 1', $result->getFiles()[0]->getMessages()[0]->getMessage());
    }

    private function reportDataProvider(): array
    {
        return [
            [
                [
                    'totals' => [
                        'errors' => 1,
                        'warnings' => 0,
                        'fixable' => 1,
                    ],
                    'errors' => [

                    ],
                    'files' => [
                        'path/to/test/report/file' => [
                            'messages' => [
                                'TestMessage 1',
                            ],
                            'errors' => 1,
                            'warnings' => 0,
                            'fixable' => 1,
                        ],
                    ],
                ]
            ],
        ];
    }
}
