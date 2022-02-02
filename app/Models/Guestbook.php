<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Model;

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
