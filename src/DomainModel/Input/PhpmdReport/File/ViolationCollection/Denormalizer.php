<?php

namespace Powercloud\SRT\DomainModel\Input\PhpmdReport\File\ViolationCollection;

use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private DenormalizerInterface $denormalizer;

    public function denormalize(
        mixed $data,
        string $type,
        string $format = null,
        array $context = []
    ): PhpmdReport\File\ViolationCollection {
        $violationCollection = new PhpmdReport\File\ViolationCollection();

        foreach ($data as $violationData) {
            $violation = $this->denormalizer->denormalize(
                $violationData,
                PhpmdReport\File\Violation::class,
                $format,
                $context
            );

            $violationCollection->add($violation);
        }

        return $violationCollection;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return $type === PhpmdReport\File\ViolationCollection::class;
    }

    public function setDenormalizer(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }
}