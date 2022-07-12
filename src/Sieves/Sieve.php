<?php

namespace Osteel\Duct\Sieves;

use Closure;
use Illuminate\Support\Str;

abstract class Sieve
{
    protected function __construct(array $options = [])
    {
    }

    public static function make(string $key, $options = []): Sieve
    {
        // @TODO check if class exists first and clean this up
        $class = sprintf('Osteel\\Duct\\Sieves\\%s', Str::studly($key));

        return (new $class())->setOptions($options);
    }

    protected function setOptions(array $options = []): static
    {
        return $this;
    }

    /**
     * Optional closure returning a boolean indicating whether the current file should be processed.
     *
     * Minimalist example:
     *
     * ```
     * public function getScreen(): Closure|null
     * {
     *     return fn (SplFileInfo $file) => true;
     * }
     * ```
     */
    abstract public function getScreen(): Closure|null;

    /**
     * Closure processing a single file.
     *
     * Minimalist example:
     *
     * ```
     * public function getProcess(): Closure
     * {
     *     return fn (SplFileInfo $file) => unlink($file->getPathname());
     * }
     * ```
     */
    abstract public function getProcess(): Closure;
}
