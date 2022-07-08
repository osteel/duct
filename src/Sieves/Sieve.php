<?php

namespace Osteel\Duct\Sieves;

use Illuminate\Support\Str;
use Osteel\Duct\Services\Interpreter;
use Osteel\Duct\ValueObjects\Directory;

abstract class Sieve
{
    public static function make(string $key, Interpreter $interpreter, array $options = []): Sieve
    {
        // @TODO check if class exists first and clean this up
        $class = sprintf('Osteel\\Duct\\Sieves\\%s', Str::studly($key));

        return new $class($interpreter, $options);
    }

    /**
     * Apply the sieve and return the number of processed files.
     */
    abstract public function filter(Directory $directory): int;
}
