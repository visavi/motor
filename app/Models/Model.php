<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Builder;

/**
 * Base model
 */
class Model extends Builder
{
    public function __construct()
    {
        // Override paginate page name
        //$this->paginateName = 'page';

        // Override paginate template
        //$this->paginateView = basePath('/resources/views/app/_paginator.php');
    }
}
