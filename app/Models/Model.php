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
     * Path of the table file
     *
     * @return string
     */
    public function getPath(): string
    {
        return storagePath('database/' . $this->table);
    }
}
