<?php

namespace Osteel\Duct\Sieves;

use Illuminate\Support\Str;
use Osteel\Duct\ValueObjects\Directory;

abstract class Sieve
{
    public static function make(string $key, array $options = []): Sieve
    {
        // @TODO check if class exists first and clean this up
        $class = sprintf('Osteel\\Duct\\Sieves\\%s', Str::studly($key));

        return new $class($options);
    }

    abstract public function filter(Directory $directory): void;
}
