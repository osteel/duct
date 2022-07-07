<?php

namespace Osteel\Duct\Services\Configurator;

use Dotenv\Dotenv;
use Dotenv\Exception\ExceptionInterface;
use Osteel\Duct\Services\Configurator\Exceptions\MissingConfiguration;

class Configurator
{
    private function getDirectoryPath(): string
    {
        return sprintf('%s/../../../config', __DIR__);
    }

    private function getFilePath(): string
    {
        return sprintf('%s/.env', $this->getDirectoryPath());
    }

    /**
     * Load a configuration value.
     *
     * @throws MissingConfiguration
     */
    public function load(?string $key = null): mixed
    {
        try {
            Dotenv::createImmutable($this->getDirectoryPath())->load();
        } catch (ExceptionInterface) {
            throw new MissingConfiguration();
        }

        if (empty($key)) {
            return $_ENV;
        }

        return $_ENV[$key] ?? throw new MissingConfiguration(sprintf('Key %s is undefined', $key));
    }

    public function save(string $key, string $value): void
    {
        $stream = fopen($this->getFilePath(), 'a');

        fwrite($stream, sprintf('%s=%s%s', $key, $value, PHP_EOL));

        fclose($stream);
    }
}
