<?php

namespace Tests\Models;

use App\Models\Model;

/**
 * Class Test2
 *
 * @property string $key
 * @property string $value
 */
class Test2 extends Model
{
    public string $filePath = __DIR__ . '/../../tests/data/test2.csv';
}
