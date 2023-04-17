<?php

declare(strict_types=1);

namespace App\Models;

use MotorORM\Builder;

/**
 * Base model
 */
class Model extends Builder
{
    /**
     * Table dir
     */
    protected ?string $tableDir = __DIR__ . '/../../storage/database';

    /**
     * Paginate page name
     */
    //protected ?string $paginateName = 'page';

    /**
     * Paginate template
     */
    //protected ?string $paginateView = __DIR__ . '/../../resources/views/app/_paginator.php';
}
