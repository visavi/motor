<?php

namespace App\Models;

/**
 * Class Test
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $text
 * @property int $time
 */
class Guestbook extends Model
{
    public string $filePath = __DIR__ . '/../../database/guestbook.csv';
}
