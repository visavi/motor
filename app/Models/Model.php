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
        // Override paginate page name
        //$this->paginateName = 'page';

        // Override paginate template
        //$this->paginateView = basePath('/resources/views/app/_paginator.php');
    }
}
