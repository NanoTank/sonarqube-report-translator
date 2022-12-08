<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\DomainModel;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\AbstractCollection;

class AbstractCollectionTest extends TestCase
{
    private AbstractCollection $testObject;

    public function setUp(): void
    {
        $this->testObject = new class extends AbstractCollection {
            public function __construct()
            {
                $this->items = [
                    'test item 1',
                    'test item 2',
                ];
            }

            public function current(): mixed
            {
                return current($this->items) ?: null;
            }
        };
    }

    public function testRemove()
    {
        $this->testObject->rewind();
        $this->testObject->remove($this->testObject->key());

        $this->testObject->rewind();
        $this->assertEquals('test item 2', $this->testObject->current());

        $this->testObject->next();

        $this->assertFalse($this->testObject->valid());
    }

    public function testCurrentAndNext()
    {
        $this->testObject->rewind();

        $this->assertEquals('test item 1', $this->testObject->current());

        $this->testObject->next();

        $this->assertEquals('test item 2', $this->testObject->current());
    }

    public function testKey()
    {
        $this->testObject->rewind();

        $this->assertEquals(0, $this->testObject->key());
        $this->testObject->next();
        $this->assertEquals(1, $this->testObject->key());
        $this->testObject->next();
        $this->assertNull($this->testObject->key());
    }

    public function testValid()
    {
        $this->testObject->rewind();

        $this->assertTrue($this->testObject->valid());
        $this->testObject->next();
        $this->assertTrue($this->testObject->valid());
        $this->testObject->next();
        $this->assertFalse($this->testObject->valid());
    }

    public function testRewind()
    {
        $this->testObject->rewind();
        $this->assertEquals(0, $this->testObject->key());
        $this->testObject->next();
        $this->assertEquals(1, $this->testObject->key());
        $this->testObject->rewind();
        $this->assertEquals(0, $this->testObject->key());
    }
}
