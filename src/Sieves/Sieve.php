<?php

namespace Osteel\Duct\Sieves;

use DirectoryIterator;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;

abstract class Sieve
{
    public static function make(string $key, array $options = []): Sieve
    {
        // @TODO check if class exists first and clean this up
        $class = sprintf('Osteel\\Duct\\Sieves\\%1$s\\%1$s', Str::studly($key));
        return new $class($options);
    }

    // @TODO add interface?
    abstract public function process(DirectoryIterator | RecursiveDirectoryIterator $directory): void;
}
