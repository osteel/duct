<?php

namespace Osteel\Duct\Services;

use DirectoryIterator;
use Dotenv\Dotenv;
use Dotenv\Exception\ExceptionInterface;
use Illuminate\Support\Collection;
use IteratorIterator;
use Osteel\Duct\Sieves\Sieve;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Plumber
{
    public function apply(string $treatment, string $directoryPath, bool $recursive = false): void
    {
        // @TODO better exception handling
        $directory = $recursive
            ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath))
            : new IteratorIterator(new DirectoryIterator($directoryPath));

        $this->load($treatment)->each(fn (Sieve $sieve) => $sieve->process($directory));
    }

    private function load(string $treatment): Collection
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

        return Collection::make($yaml['treatments'][$treatment])
            ->map(fn (array $parameters, string $key) => Sieve::make($key, $parameters));
    }
}