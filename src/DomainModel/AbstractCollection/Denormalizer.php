<?php

namespace Powercloud\SRT\DomainModel\AbstractCollection;

use Powercloud\SRT\DomainModel\AbstractCollection;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private DenormalizerInterface $denormalizer;

    public function denormalize(
        mixed $data,
        string $type,
        string $format = null,
        array $context = [],
    ): AbstractCollection {
        $classReflection = new \ReflectionClass($type);
        $currentMethod = $classReflection->getMethod('current');
        $currentMethodReturnType = $currentMethod->getReturnType()?->getName();

        $collection = new $type();

        foreach ($data as $itemData) {
            $item = $this->denormalizer->denormalize(
                $itemData,
                $currentMethodReturnType,
                $format,
                $context,
            );

            $collection->add($item);
        }

        return $collection;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        try {
            $classReflection = new \ReflectionClass($type);
            if (!$classReflection->isSubclassOf(AbstractCollection::class)) {
                return false;
            }
            $currentMethod = $classReflection->getMethod('current');
            $currentMethodReturnType = $currentMethod->getReturnType()?->getName();

            if (is_null($currentMethodReturnType)) {
                return false;
            }

            return true;
        } catch (\ReflectionException) {
            return false;
        }
    }

    public function setDenormalizer(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }
}