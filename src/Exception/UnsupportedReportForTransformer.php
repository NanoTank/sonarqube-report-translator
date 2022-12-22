<?php

declare(strict_types=1);

namespace Powercloud\SRT\Exception;

class UnsupportedReportForTransformer extends \Exception
{
    public function __construct(
        string $message = "Report of this format is not supported by this transformer",
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
