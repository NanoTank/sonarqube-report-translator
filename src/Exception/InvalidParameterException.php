<?php

declare(strict_types=1);

namespace Powercloud\SRT\Exception;

use Throwable;

class InvalidParameterException extends \ErrorException
{
    public function __construct(
        string $message = "Invalid parameter provided",
        int $code = 0,
        int $severity = 1,
        string $filename = __FILE__,
        int $line = __LINE__,
        Throwable|null $previous = null
    ) {
        parent::__construct(
            $message,
            $code,
            $severity,
            $filename,
            $line,
            $previous
        );
    }
}
