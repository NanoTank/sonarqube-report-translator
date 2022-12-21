<?php

namespace Powercloud\SRT\DomainModel\Input\DeptracReport;

use Powercloud\SRT\DomainModel\Input\DeptracReport;
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
        /** @var DeptracReport\Report $report */
        $report = $this->denormalizer->denormalize(
            $data['Report'] ?? [],
            DeptracReport\Report::class,
            $format,
            $context,
        );
        /** @var DeptracReport\File[] $files */
        $files = [];

        foreach ($data['files'] ?? [] as $filePath => $fileIssues) {
            /** @var DeptracReport\File\Message[] $messages */
            $messages = [];
            foreach ($fileIssues['messages'] ?? [] as $messageData) {
                /** @var DeptracReport\File\Message $message */
                $message = $this->denormalizer->denormalize(
                    $messageData,
                    DeptracReport\File\Message::class,
                    $format,
                    $context,
                );

                $messages[] = $message;
            }

            $files[] = new DeptracReport\File($fileIssues['violations'] ?? 0, $messages, $filePath);
        }

        return new DeptracReport($report, $files);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        string $format = null,
        array $context = []
    ): bool {
        return $type === DeptracReport::class;
    }

    public function setDenormalizer(DenormalizerInterface $denormalizer): void
    {
        $this->denormalizer = $denormalizer;
    }
}
