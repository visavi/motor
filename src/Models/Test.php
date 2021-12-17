<?php

namespace App\Models;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $text
 * @property int $time
 */
class Test extends \App\Model
{
    public string $filePath = __DIR__ . '/../../tests/data/test.csv';
}
