<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\DomainModel\Denormalizer;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Denormalizer\PhpmdReportDenormalizer;
use Powercloud\SRT\DomainModel\Input\PhpmdReport;

class PhpmdReportDenormalizerTest extends TestCase
{
    private PhpmdReportDenormalizer $testObject;
    public function setUp(): void
    {
        $this->testObject = new PhpmdReportDenormalizer();
    }

    public function testSupportsCorrectObject(): void
    {
        $this->assertTrue($this->testObject->supportsDenormalization([], PhpmdReport::class));
        $this->assertFalse($this->testObject->supportsDenormalization([], \stdClass::class));
    }

    /**
     * @dataProvider contentProvider
     */
    public function testDenormalizeWorksCorrectly(array $content): void
    {
        $denormalized = $this->testObject->denormalize($content, PhpmdReport::class);

        $this->assertSame($content['version'], $denormalized->getVersion());
        $this->assertSame($content['package'], $denormalized->getPackage());
        $this->assertSame($content['timestamp'], $denormalized->getTimestamp());
        $this->assertInstanceOf(PhpmdReport\FileCollection::class, $denormalized->getFiles());

        foreach ($denormalized->getFiles() as $keyFile => $file) {
            $this->assertSame($content['files'][$keyFile]['file'], $file->getFile());
            $this->assertInstanceOf(PhpmdReport\File\ViolationCollection::class, $file->getViolations());
            foreach ($file->getViolations() as $keyViolation => $violation) {
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['beginLine'],
                    $violation->getBeginLine()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['endLine'],
                    $violation->getEndLine()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['package'],
                    $violation->getPackage()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['function'],
                    $violation->getFunction()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['class'],
                    $violation->getClass()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['method'],
                    $violation->getMethod()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['description'],
                    $violation->getDescription()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['rule'],
                    $violation->getRule()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['ruleSet'],
                    $violation->getRuleSet()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['externalInfoUrl'],
                    $violation->getExternalInfoUrl()
                );
                $this->assertSame(
                    $content['files'][$keyFile]['violations'][$keyViolation]['priority'],
                    $violation->getPriority()
                );
            }
        }
    }

    protected function contentProvider(): array
    {
        return [[
            [
                'version' => 'TestVersion',
                'package' => 'TestPackage',
                'timestamp' => '2022-12-07T10:10:10+00:00',
                'files' => [
                    0 => [
                        'file' => 'path/to/file1',
                        'violations' => [
                            0 => [
                                'beginLine' => 1,
                                'endLine' => 25,
                                'package' => 'TestPackage 1',
                                'function' => 'TestFunction 1',
                                'class' => 'TestClass 1',
                                'method' => 'TestMethod 1',
                                'description' => 'TestDescription 1',
                                'rule' => 'TestRule 1',
                                'ruleSet' => 'TestRuleSet 1',
                                'externalInfoUrl' => 'TestExternalInfoUrl 1',
                                'priority' => 1,
                            ],
                            1 => [
                                'beginLine' => 15,
                                'endLine' => 17,
                                'package' => 'TestPackage 1',
                                'function' => 'TestFunction 1',
                                'class' => 'TestClass 1',
                                'method' => 'TestMethod 1-1',
                                'description' => 'TestDescription 1-1',
                                'rule' => 'TestRule 1-1',
                                'ruleSet' => 'TestRuleSet 1-1',
                                'externalInfoUrl' => 'TestExternalInfoUrl 1-1',
                                'priority' => 2,
                            ],
                        ]
                    ],
                    1 => [
                        'file' => 'path/to/file2',
                        'violations' => [
                            0 => [
                                'beginLine' => 100,
                                'endLine' => 25,
                                'package' => 'TestPackage 2',
                                'function' => 'TestFunction 2',
                                'class' => 'TestClass 2',
                                'method' => 'TestMethod 2',
                                'description' => 'TestDescription 2',
                                'rule' => 'TestRule 2',
                                'ruleSet' => 'TestRuleSet 2',
                                'externalInfoUrl' => 'TestExternalInfoUrl 2',
                                'priority' => 3,
                            ],
                            1 => [
                                'beginLine' => 151,
                                'endLine' => 171,
                                'package' => 'TestPackage 2',
                                'function' => 'TestFunction 2-2',
                                'class' => 'TestClass 2-2',
                                'method' => 'TestMethod 2-2',
                                'description' => 'TestDescription 2-2',
                                'rule' => 'TestRule 2-2',
                                'ruleSet' => 'TestRuleSet 2-2',
                                'externalInfoUrl' => 'TestExternalInfoUrl 2-2',
                                'priority' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ]];
    }
}
