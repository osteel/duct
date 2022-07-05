<?php

namespace Osteel\Duct\Sieves\Utils;

use FilterIterator;
use Iterator;

class ExtensionFilter extends FilterIterator
{
    private static array $extensionMap = [
        'heic' => ['heic', 'heif'],
        'heif' => ['heic', 'heif'],
        'jpeg' => ['jpg', 'jpeg'],
        'jpg' => ['jpg', 'jpeg'],
    ];

    // @TODO support several extensions
    public function __construct(Iterator $iterator, private array $extensions)
    {
        // @TODO filter supported extensions
        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        $extensions = [];

        foreach ($this->extensions as $current) {
            $extensions = array_merge($extensions, self::$extensionMap[$current] ?? [$this->extension]);
        }

        return in_array(strtolower($this->current()->getExtension()), $extensions);
    }
}
