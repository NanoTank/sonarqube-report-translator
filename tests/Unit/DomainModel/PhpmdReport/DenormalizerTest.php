<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\DomainModel\PhpmdReport;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Powercloud\SRT\DomainModel\Input\PhpmdReport\Denormalizer;
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
        $this->assertTrue($this->testObject->supportsDenormalization([], PhpmdReport::class));
        $this->assertFalse($this->testObject->supportsDenormalization([], \stdClass::class));
    }

    /**
     * @dataProvider reportDataProvider
     */
    public function testDenormalizeWorksCorrectly(array $data): void
    {
        $violation = new PhpmdReport\File\Violation(
            beginLine: $data['files'][0]['violations'][0]['beginLine'],
            endLine: $data['files'][0]['violations'][0]['endLine'],
            package: $data['files'][0]['violations'][0]['package'],
            function: $data['files'][0]['violations'][0]['function'],
            class: $data['files'][0]['violations'][0]['class'],
            method: $data['files'][0]['violations'][0]['method'],
            description: $data['files'][0]['violations'][0]['description'],
            rule: $data['files'][0]['violations'][0]['rule'],
            ruleSet: $data['files'][0]['violations'][0]['ruleSet'],
            externalInfoUrl: $data['files'][0]['violations'][0]['externalInfoUrl'],
            priority: $data['files'][0]['violations'][0]['priority']
        );

        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->willReturnCallback(
                function ($data, $type) use ($violation) {
                    return match ($type) {
                        PhpmdReport\File\Violation::class => $violation,
                        default => new \stdClass()
                    };
                }
            );

        $this->testObject->setDenormalizer($denormalizer);
        $result = $this->testObject->denormalize($data, PhpmdReport::class);

        $this->assertSame($data['version'], $result->getVersion());
        $this->assertSame($data['package'], $result->getPackage());
        $this->assertSame($data['timestamp'], $result->getTimestamp());

        $this->assertCount(1, $result->getFiles());
        $this->assertSame($data['files'][0]['file'], $result->getFiles()[0]->getFile());

        $this->assertCount(1, $result->getFiles()[0]->getViolations());
        $this->assertSame(
            $data['files'][0]['violations'][0]['beginLine'],
            $result->getFiles()[0]->getViolations()[0]->getBeginLine()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['endLine'],
            $result->getFiles()[0]->getViolations()[0]->getEndLine()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['package'],
            $result->getFiles()[0]->getViolations()[0]->getPackage()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['function'],
            $result->getFiles()[0]->getViolations()[0]->getFunction()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['class'],
            $result->getFiles()[0]->getViolations()[0]->getClass()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['method'],
            $result->getFiles()[0]->getViolations()[0]->getMethod()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['description'],
            $result->getFiles()[0]->getViolations()[0]->getDescription()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['rule'],
            $result->getFiles()[0]->getViolations()[0]->getRule()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['ruleSet'],
            $result->getFiles()[0]->getViolations()[0]->getRuleSet()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['externalInfoUrl'],
            $result->getFiles()[0]->getViolations()[0]->getExternalInfoUrl()
        );
        $this->assertSame(
            $data['files'][0]['violations'][0]['priority'],
            $result->getFiles()[0]->getViolations()[0]->getPriority()
        );
    }

    private function reportDataProvider(): array
    {
        return [
            [
                [
                    'version' => '@package_test_version@',
                    'package' => 'phpmd',
                    'timestamp' => "2022-12-22T10:17:57+00:00",
                    'files' => [
                        [
                            'file' => '\/app\/TestMe.php',
                            'violations' => [
                                [
                                    'beginLine' => 1,
                                    'endLine' => 1,
                                    'package' => 'Test Package',
                                    'function' => 'testFunction',
                                    'class' => 'testClass',
                                    'method' => 'testMethod',
                                    'description' => 'Avoid unused private fields such as \u0027$r\u0027.',
                                    'rule' => 'UnusedPrivateField',
                                    'ruleSet' => 'Unused Code Rules',
                                    'externalInfoUrl'
                                        => 'https:\/\/phpmd.org\/rules\/unusedcode.html#unusedprivatefield',
                                    'priority' => 3
                                ],
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }
}
