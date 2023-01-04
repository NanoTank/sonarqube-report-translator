<?php

namespace Powercloud\SRT\DomainModel\Input\DeptracReport;

use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Symfony\Component\Serializer\Exception\LogicException;
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
    ): DeptracReport {
        if (DeptracReport::class !== $type) {
            throw new LogicException(
                sprintf(
                    'Expected type of %s, %s given',
                    DeptracReport::class,
                    $type
                )
            );
        }

        /** @var DeptracReport\Report $report */
        $report = $this->denormalizer->denormalize(
            data: $data['Report'] ?? [],
            type: DeptracReport\Report::class,
            format: $format,
            context: $context,
        );
        /** @var DeptracReport\File[] $files */
        $files = [];

        foreach ($data['files'] ?? [] as $filePath => $fileIssues) {
            /** @var DeptracReport\File\Message[] $messages */
            $messages = [];
            foreach ($fileIssues['messages'] ?? [] as $messageData) {
                /** @var DeptracReport\File\Message $message */
                $message = $this->denormalizer->denormalize(
                    data: $messageData,
                    type: DeptracReport\File\Message::class,
                    format: $format,
                    context: $context,
                );

                $messages[] = $message;
            }

            $files[] = new DeptracReport\File($fileIssues['violations'] ?? 0, $messages, $filePath);
        }

        return new DeptracReport(
            report: $report,
            files: $files
        );
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function supportsDenormalization(
        mixed $data,
        string $type,
        string $format = null,
        array $context = []
    ): bool {
        return $type === DeptracReport::class;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDenormalizer(DenormalizerInterface $denormalizer): void
    {
        $this->denormalizer = $denormalizer;
    }
}
