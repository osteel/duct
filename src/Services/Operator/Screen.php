<?php

namespace Osteel\Duct\Services\Operator;

use Closure;
use FilterIterator;
use Iterator;

final class Screen extends FilterIterator
{
    public function __construct(Iterator $iterator, private readonly Closure $screen)
    {
        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        $screen = $this->screen;

        return $screen($this->current());
    }
}
