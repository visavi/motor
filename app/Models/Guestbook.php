<?php

namespace App\Models;

/**
 * Class Guestbook
 *
 * @property int $id
 * @property string $login
 * @property string $title
 * @property string $text
 * @property string $image
 * @property int $created_at
 */
class Guestbook extends Model
{
    public string $filePath = __DIR__ . '/../../database/guestbook.csv';
}
