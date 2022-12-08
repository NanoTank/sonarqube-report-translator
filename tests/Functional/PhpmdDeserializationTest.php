<?php
declare(strict_types=1);

namespace Powercloud\SRT\Tests\Functional;

use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class PhpmdDeserializationTest extends KernelTestCase
{
    public function setUp(): void
    {
    }

    public function testDeserializationWorksCorrectly(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);

        $fileContent = file_get_contents(__DIR__ . '/../TestFiles/phpmd.json');

        $result = $serializer->deserialize($fileContent, PhpmdReport::class, 'json');

        dd($result);
    }
}
