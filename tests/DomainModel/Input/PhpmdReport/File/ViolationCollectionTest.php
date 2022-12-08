<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\DomainModel\Input\PhpmdReport\File;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\PhpmdReport\File\Violation;
use Powercloud\SRT\DomainModel\Input\PhpmdReport\File\ViolationCollection;

class ViolationCollectionTest extends TestCase
{
    private Violation $violation1;

    private Violation $violation2;

    private ViolationCollection $testObject;

    public function setUp(): void
    {
        $this->violation1 = new Violation(
            beginLine: 1,
            endLine: 2,
            package: 'Src',
            function: 'test',
            class: 'class',
            method: 'test()',
            description: 'some description',
            rule: 'none',
            ruleSet: 'Important!!!',
            externalInfoUrl: 'http://example.com',
            priority: 3,
        );

        $this->violation2 = new Violation(
            beginLine: 3,
            endLine: 4,
            package: 'Src\\Tst',
            function: 'test2',
            class: 'class2',
            method: 'test2()',
            description: 'some description2',
            rule: 'none what so ever',
            ruleSet: 'Almost important',
            externalInfoUrl: 'http://example.com',
            priority: 2,
        );

        $this->testObject = new ViolationCollection();
    }

    public function testAddWithWrongTypeThrowsErrorException(): void
    {
        $this->expectError();
        $this->expectErrorMessage(
            sprintf(
                'Argument #1 ($violation) must be of type %s, class@anonymous given',
                Violation::class,
            ),
        );

        $this->testObject->add(
            new class
            {
            },
        );
    }

    public function testAddMultiple(): void
    {
        $this->testObject->add($this->violation1);
        $this->testObject->add($this->violation2);

        $this->testObject->rewind();
        $this->assertSame($this->violation1, $this->testObject->current());
        $this->testObject->next();
        $this->assertSame($this->violation2, $this->testObject->current());
    }

    public function testCurrentAndNext()
    {
        $this->testObject->add($this->violation1);
        $this->testObject->add($this->violation2);
        $this->testObject->rewind();

        $this->assertSame($this->violation1, $this->testObject->current());

        $this->testObject->next();

        $this->assertSame($this->violation2, $this->testObject->current());
    }
}
