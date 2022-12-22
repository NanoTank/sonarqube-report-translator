<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpmdReport;

use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private DenormalizerInterface $denormalizer;

    public function supportsDenormalization(
        mixed  $data,
        string $type,
        string $format = null,
        array  $context = []
    ): bool {
        return $type === PhpmdReport::class;
    }

    public function denormalize(
        mixed  $data,
        string $type,
        string $format = null,
        array  $context = []
    ): PhpmdReport {
        /** @var File[] $files */
        $files = [];
        foreach ($data['files'] as $file) {
            /** @var PhpmdReport\File\Violation[] $violations */
            $violations = [];
            foreach ($file['violations'] as $violationData) {
                $violations[] = $this->denormalizer->denormalize(
                    data: $violationData,
                    type: PhpmdReport\File\Violation::class,
                    format: $format,
                    context: $context
                );
            }

            $files[] = new File(
                file: $file['file'],
                violations: $violations
            );
        }

        return new PhpmdReport(
            version: $data['version'],
            package: $data['package'],
            timestamp: $data['timestamp'],
            files: $files
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDenormalizer(DenormalizerInterface $denormalizer): void
    {
        $this->denormalizer = $denormalizer;
    }
}
