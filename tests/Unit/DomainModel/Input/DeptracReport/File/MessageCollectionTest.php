<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Unit\DomainModel\Input\DeptracReport\File;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\DeptracReport\File\Message;
use Powercloud\SRT\DomainModel\Input\DeptracReport\File\MessageCollection;

class MessageCollectionTest extends TestCase
{
    private Message $message1;

    private Message $message2;

    private MessageCollection $testObject;

    public function setUp(): void
    {
        $this->message1 = new Message(
            message: 'Test Message 1',
            line: 10,
            type: Message\TypeEnum::Error
        );
        $this->message2 = new Message(
            message: 'Test Message 2',
            line: 22,
            type: Message\TypeEnum::Warning
        );

        $this->testObject = new MessageCollection();
    }

    public function testAddWithWrongTypeThrowsErrorException(): void
    {
        $this->expectError();
        $this->expectErrorMessage(
            sprintf(
                'Argument #1 ($message) must be of type %s, class@anonymous given',
                Message::class,
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
        $this->testObject->add($this->message1);
        $this->testObject->add($this->message2);

        $this->testObject->rewind();
        $this->assertSame($this->message1, $this->testObject->current());
        $this->testObject->next();
        $this->assertSame($this->message2, $this->testObject->current());
    }

    /**
     * @depends testAddMultiple
     */
    public function testCurrentAndNext()
    {
        $this->testObject->add($this->message1);
        $this->testObject->add($this->message2);
        $this->testObject->rewind();

        $this->assertSame($this->message1, $this->testObject->current());

        $this->testObject->next();

        $this->assertSame($this->message2, $this->testObject->current());
    }
}
