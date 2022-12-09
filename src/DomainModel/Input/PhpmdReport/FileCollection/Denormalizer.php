<?php

namespace Powercloud\SRT\DomainModel\Input\PhpmdReport\FileCollection;

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
    ): PhpmdReport\FileCollection {
        $fileCollection = new PhpmdReport\FileCollection();

        foreach ($data as $fileData) {
            $file = $this->denormalizer->denormalize($fileData, PhpmdReport\File::class, $format, $context);

            $fileCollection->add($file);
        }

        return $fileCollection;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return $type === PhpmdReport\FileCollection::class;
    }

    public function setDenormalizer(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }
}