<?php

namespace Osteel\Duct\ValueObjects;

use Illuminate\Support\Collection;
use Osteel\Duct\Services\Configurator\Configurator;
use Osteel\Duct\Services\Interpreter;
use Osteel\Duct\Sieves\Sieve;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class Treatment
{
    private function __construct(public readonly Collection $sieves)
    {
    }

    public static function make(string $treatment, Interpreter $interpreter): Treatment
    {
        $configurator = new Configurator();

        try {
            $yaml = Yaml::parseFile($configurator->load('TREATMENTS_LOCATION'));
        } catch (ParseException) {
            // @TODO do something
        }

        if (! is_array($yaml) || empty($yaml['treatments']) || empty($yaml['treatments'][$treatment])) {
            // @TODO do something
        }

        $sieves = Collection::make($yaml['treatments'][$treatment])
            ->map(fn (array $options, string $key) => Sieve::make($key, $interpreter, $options));

        return new Treatment($sieves);
    }
}
