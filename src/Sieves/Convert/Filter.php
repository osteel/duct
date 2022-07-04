<?php

namespace Osteel\Duct\Sieves\Convert;

use FilterIterator;
use Iterator;

class Filter extends FilterIterator
{
    private static array $extensions = [
        'heic' => ['heic', 'heif'],
        'heif' => ['heic', 'heif'],
        'jpeg' => ['jpg', 'jpeg'],
        'jpg' => ['jpg', 'jpeg'],
    ];

    // @TODO support several extensions
    public function __construct(Iterator $iterator, private string $extension)
    {
        // @TODO filter supported extensions
        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        return in_array(strtolower($this->current()->getExtension()), self::$extensions[$this->extension]);
    }
}
