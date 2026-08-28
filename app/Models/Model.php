<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Model as BaseModel;

/**
 * Base model
 */
abstract class Model extends BaseModel
{
    /**
     * Table dir
     */
    protected ?string $tableDir = __DIR__ . '/../../storage/database';
}
