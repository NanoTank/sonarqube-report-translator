<?php

namespace Powercloud\SRT\DomainModel\Input\PhpcsReport;

use Powercloud\SRT\DomainModel\Input\PhpcsReport;
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
    ): PhpcsReport {
        if (!isset($data['totals'])) {
            throw new \LogicException('Missing [totals] in the phpcs report data');
        }

        /** @var PhpcsReport\Totals $report */
        $totals = $this->denormalizer->denormalize(
            data: $data['totals'],
            type: PhpcsReport\Totals::class,
            format: $format,
            context: $context,
        );
        /** @var PhpcsReport\File[] $files */
        $files = [];

        foreach ($data['files'] ?? [] as $filePath => $fileIssues) {
            /** @var PhpcsReport\File\Message[] $messages */
            $messages = [];
            foreach ($fileIssues['messages'] ?? [] as $messageData) {
                /** @var PhpcsReport\File\Message $message */
                $message = $this->denormalizer->denormalize(
                    data: $messageData,
                    type: PhpcsReport\File\Message::class,
                    format: $format,
                    context: $context,
                );

                $messages[] = $message;
            }

            $files[] = new PhpcsReport\File(
                errors: $fileIssues['errors'],
                warnings: $fileIssues['warnings'],
                messages: $messages,
                path: $filePath
            );
        }

        return new PhpcsReport(
            totals: $totals,
            files: $files
        );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        string $format = null,
        array $context = []
    ): bool {
        return $type === PhpcsReport::class;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDenormalizer(DenormalizerInterface $denormalizer): void
    {
        $this->denormalizer = $denormalizer;
    }
}
