<?php

namespace Osteel\Duct\ValueObjects;

use DirectoryIterator;
use Iterator;
use IteratorIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class Directory
{
    private function __construct(public readonly Iterator $iterator)
    {
    }

    public static function make(string $path, bool $recursive = false): Directory
    {
        // @TODO handle exceptions
        $iterator = $recursive
            ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path))
            : new IteratorIterator(new DirectoryIterator($path));

        return new Directory($iterator);
    }
}
