<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\DomainModel\Input\PhpcsReport\File;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\PhpcsReport\File\Message;
use Powercloud\SRT\DomainModel\Input\PhpcsReport\File\MessageCollection;

class MessageCollectionTest extends TestCase
{
    private Message $message1;

    private Message $message2;

    private MessageCollection $testObject;

    public function setUp(): void
    {
        $this->message1 = new Message(
            message: 'message 1',
            source: 'source 1',
            severity: 5,
            fixable: true,
            type: Message\TypeEnum::Error,
            line: 200,
            column: 2,
        );

        $this->message2 = new Message(
            message: 'message 2',
            source: 'source 2',
            severity: 4,
            fixable: false,
            type: Message\TypeEnum::Warning,
            line: 300,
            column: 5,
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
