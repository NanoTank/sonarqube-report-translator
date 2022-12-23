<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\DomainModel\PhpcsReport;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Input\PhpcsReport\Denormalizer;
use stdClass;
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
        $this->assertFalse($this->testObject->supportsDenormalization([], stdClass::class));
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
            errors: $data['totals']['errors'],
            warnings: $data['totals']['warnings'],
            fixable: $data['totals']['fixable']
        );

        $messageKey = '';
        foreach ($data['files'] as $dataFileKey => $dataFileValues) {
            $messageKey = $dataFileKey;
            $message = new PhpcsReport\File\Message(
                message: $data['files'][$dataFileKey]['messages'][0]['message'],
                source: $data['files'][$dataFileKey]['messages'][0]['source'],
                severity: $data['files'][$dataFileKey]['messages'][0]['severity'],
                fixable: $data['files'][$dataFileKey]['messages'][0]['fixable'],
                type: PhpcsReport\File\Message\TypeEnum::Error,
                line: $data['files'][$dataFileKey]['messages'][0]['line'],
                column: $data['files'][$dataFileKey]['messages'][0]['column']
            );
            break;
        }


        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer
            ->expects(self::exactly(2))
            ->method('denormalize')
            ->willReturnCallback(
                function ($data, $type) use ($totals, $message) {
                    return match ($type) {
                        PhpcsReport\Totals::class => $totals,
                        PhpcsReport\File\Message::class => $message,
                        default => new stdClass()
                    };
                }
            );

        $this->testObject->setDenormalizer($denormalizer);

        $result = $this->testObject->denormalize($data, PhpcsReport::class);

        $this->assertSame($data['totals']['errors'], $result->getTotals()->getErrors());
        $this->assertSame($data['totals']['warnings'], $result->getTotals()->getWarnings());
        $this->assertSame($data['totals']['fixable'], $result->getTotals()->getFixable());

        $this->assertCount(1, $result->getFiles());
        $this->assertSame($messageKey, $result->getFiles()[0]->getPath());

        $this->assertCount(1, $result->getFiles()[0]->getMessages());
        $this->assertSame(
            PhpcsReport\File\Message\TypeEnum::Error,
            $result->getFiles()[0]->getMessages()[0]->getType()
        );
        $this->assertSame(
            $data['files'][$messageKey]['messages'][0]['message'],
            $result->getFiles()[0]->getMessages()[0]->getMessage()
        );
        $this->assertSame(
            $data['files'][$messageKey]['messages'][0]['source'],
            $result->getFiles()[0]->getMessages()[0]->getSource()
        );
        $this->assertSame(
            $data['files'][$messageKey]['messages'][0]['severity'],
            $result->getFiles()[0]->getMessages()[0]->getSeverity()
        );
        $this->assertSame(
            $data['files'][$messageKey]['messages'][0]['fixable'],
            $result->getFiles()[0]->getMessages()[0]->isFixable()
        );
        $this->assertSame(
            PhpcsReport\File\Message\TypeEnum::Error,
            $result->getFiles()[0]->getMessages()[0]->getType()
        );
        $this->assertSame(
            $data['files'][$messageKey]['messages'][0]['line'],
            $result->getFiles()[0]->getMessages()[0]->getLine()
        );
        $this->assertSame(
            $data['files'][$messageKey]['messages'][0]['column'],
            $result->getFiles()[0]->getMessages()[0]->getColumn()
        );
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
                    'files' => [
                        'path/to/test/report/file' => [
                            'messages' => [
                                [
                                    'message' => 'Header blocks must be separated by a single blank line',
                                    'source' => 'PSR12.Files.FileHeader.SpacingAfterBlock',
                                    'severity' => 5,
                                    'fixable' => true,
                                    'type' => 'ERROR',
                                    'line' => 3,
                                    'column' => 24
                                ],
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
