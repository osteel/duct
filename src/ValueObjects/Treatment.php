<?php

namespace Osteel\Duct\ValueObjects;

use Dotenv\Dotenv;
use Dotenv\Exception\ExceptionInterface;
use Illuminate\Support\Collection;
use Osteel\Duct\Sieves\Sieve;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class Treatment
{
    private function __construct(public readonly Collection $sieves)
    {
    }

    public static function make(string $treatment): Treatment
    {
        try {
            $config = Dotenv::createImmutable(sprintf('%s/../../config', __DIR__));
            $config->load();
        } catch (ExceptionInterface) {
            // @TODO run config command instead
        }

        try {
            $yaml = Yaml::parseFile($_ENV['TREATMENTS_FILE']);
        } catch (ParseException) {
            // @TODO do something
        }

        if (! is_array($yaml) || empty($yaml['treatments']) || empty($yaml['treatments'][$treatment])) {
            // @TODO do something
        }

        $sieves = Collection::make($yaml['treatments'][$treatment])
            ->map(fn (array $parameters, string $key) => Sieve::make($key, $parameters));

        return new Treatment($sieves);
    }
}
