<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class MigrationException extends RuntimeException
{
    public function __construct(
        public readonly string $migration,
        Throwable $previous,
    ) {
        parent::__construct($migration . ': ' . $previous->getMessage(), 0, $previous);
    }
}
