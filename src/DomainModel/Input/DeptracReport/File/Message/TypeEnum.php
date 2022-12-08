<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\DeptracReport\File\Message;

/**
 * @codeCoverageIgnore
 */
enum TypeEnum: string
{
    case Error = 'error';
    case Warning  = 'warning';
}
