<?php

namespace Powercloud\SRT\DomainModel\Input\PhpcsReport;

use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @psalm-suppress MissingConstructor
 */
class Denormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private DenormalizerInterface $denormalizer;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ExceptionInterface
     */
    public function denormalize(
        mixed $data,
        string $type,
        string $format = null,
        array $context = [],
    ): PhpcsReport {
        if (PhpcsReport::class !== $type) {
            throw new LogicException(
                sprintf(
                    'Expected type of [%s], [%s] given',
                    PhpcsReport::class,
                    $type,
                )
            );
        }

        if (false === is_array($data)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Unsupported format for argument \$data. Expected [array], received [%s]",
                    get_debug_type($data),
                )
            );
        }

        if (!isset($data['totals'])) {
            throw new UnexpectedValueException('Missing [totals] in the phpcs report data');
        }

        /** @var PhpcsReport\Totals $totals */
        $totals = $this->denormalizer->denormalize(
            data: $data['totals'],
            type: PhpcsReport\Totals::class,
            format: $format,
            context: $context,
        );
        /** @var PhpcsReport\File[] $files */
        $files = [];

        /**
         * @var string $filePath
         * @var array $fileIssues
         */
        foreach ($data['files'] ?? [] as $filePath => $fileIssues) {
            /** @var PhpcsReport\File\Message[] $messages */
            $messages = [];
            /** @var array $messageData */
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
                errors: (int) $fileIssues['errors'],
                warnings: (int) $fileIssues['warnings'],
                messages: $messages,
                path: $filePath
            );
        }

        return new PhpcsReport(
            totals: $totals,
            files: $files
        );
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function supportsDenormalization(
        mixed $data,
        string $type,
        string $format = null,
        array $context = [],
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
