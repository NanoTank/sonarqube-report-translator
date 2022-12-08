<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpcsReport\File\Message;

/**
 * @codeCoverageIgnore
 */
enum TypeEnum: string
{
    case Error = 'ERROR';
    case Warning  = 'WARNING';
}
