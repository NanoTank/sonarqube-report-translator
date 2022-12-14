<?php

namespace Powercloud\SRT\DomainModel\Input\PhpcsReport;

use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private DenormalizerInterface $denormalizer;

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): PhpcsReport
    {
        if (!isset($data['totals'])) {
            throw new \LogicException('Missing `totals` in the phpcs report data');
        }

        /** @var PhpcsReport\Totals $report */
        $totals = $this->denormalizer->denormalize(
            $data['totals'],
            PhpcsReport\Totals::class,
            $format,
            $context,
        );
        /** @var PhpcsReport\File[] $files */
        $files = [];

        foreach ($data['files'] ?? [] as $filePath => $fileIssues) {
            /** @var PhpcsReport\File\Message[] $messages */
            $messages = [];
            foreach ($fileIssues['messages'] ?? [] as $messageData) {
                /** @var PhpcsReport\File\Message $message */
                $message = $this->denormalizer->denormalize(
                    $messageData,
                    PhpcsReport\File\Message::class,
                    $format,
                    $context,
                );

                $messages[] = $message;
            }

            $files[] = new PhpcsReport\File($fileIssues['errors'], $fileIssues['warnings'], $messages , $filePath);
        }

        return new PhpcsReport($totals, $files);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $type === PhpcsReport::class;
    }

    public function setDenormalizer(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }
}