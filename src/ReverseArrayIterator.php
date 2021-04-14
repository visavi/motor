<?php

namespace App;

use ArrayIterator;

/**
 * Class ReverseArrayIterator
 */
class ReverseArrayIterator extends ArrayIterator
{
    /**
     * ReverseArrayIterator constructor.
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        parent::__construct(array_reverse($array));
    }
}

