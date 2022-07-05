<?php

namespace Osteel\Duct\Sieves;

use Illuminate\Support\Str;
use IteratorIterator;
use RecursiveIteratorIterator;

abstract class Sieve
{
    public static function make(string $key, array $options = []): Sieve
    {
        // @TODO check if class exists first and clean this up
        $class = sprintf('Osteel\\Duct\\Sieves\\%s', Str::studly($key));
        return new $class($options);
    }

    // @TODO add interface?
    abstract public function process(IteratorIterator | RecursiveIteratorIterator $directory): void;
}
