<?php

namespace App\Models;

/**
 * Class User
 *
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $email
 * @property string $name
 * @property int $created_at
 */
class User extends Model
{
    public string $filePath = __DIR__ . '/../../database/users.csv';
}
