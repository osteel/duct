<?php

namespace Osteel\Duct\ValueObjects;

use Illuminate\Support\Collection;
use Osteel\Duct\Sieves\Sieve;

final class Treatment
{
    private function __construct(public readonly Collection $sieves)
    {
    }

    public static function make(array $sieves): Treatment
    {
        $sieves = Collection::make($sieves)->map(fn (array $options, string $key) => Sieve::make($key, $options));

        return new Treatment($sieves);
    }
}
