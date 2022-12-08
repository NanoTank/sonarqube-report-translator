<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Denormalizer;

use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PhpmdReportDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        $fileCollection = new PhpmdReport\FileCollection();
        foreach ($data['files'] as $file) {
            $violationCollection = new PhpmdReport\File\ViolationCollection();
            foreach ($file['violations'] as $violation) {
                $violationCollection->add(
                    new PhpmdReport\File\Violation(
                        beginLine: (int) $violation['beginLine'],
                        endLine: (int) $violation['endLine'],
                        package: (string) $violation['package'],
                        function: (string) $violation['function'],
                        class: (string) $violation['class'],
                        method: (string) $violation['method'],
                        description: (string) $violation['description'],
                        rule: (string) $violation['rule'],
                        ruleSet: (string) $violation['ruleSet'],
                        externalInfoUrl: (string) $violation['externalInfoUrl'],
                        priority: (int) $violation['priority'],
                    )
                );
            }
            $fileCollection->add(
                new PhpmdReport\File(
                    file: $file['file'],
                    violations: $violationCollection));

        }

        return new PhpmdReport(
            version: $data['version'],
            package: $data['package'],
            timestamp: $data['timestamp'],
            files: $fileCollection
        );
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_a($type, PhpmdReport::class, true);
    }
}
