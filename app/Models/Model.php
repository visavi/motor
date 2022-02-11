<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Base model
 */
class Model extends \MotorORM\Model
{
    public function __construct()
    {
        //$this->view = basePath('/resources/views/app/_paginator.php');
    }
}
