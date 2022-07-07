<?php

namespace Osteel\Duct\Tests\Sieves;

use DirectoryIterator;
use IteratorIterator;
use Osteel\Duct\Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Copy a directory and its content.
     */
    protected function cpdir(string $source, string $destination): void
    {
        if (is_dir($destination)) {
            $this->rmdir($destination);
        }

        mkdir($destination, recursive: true);

        $directory = new IteratorIterator(new DirectoryIterator($source));

        /** @var SplFileInfo */
        foreach ($directory as $file) {
            if ($file->isDir()) {
                continue;
            }

            $filename = sprintf('%s/%s', $destination, $file->getFilename());

            copy($file->getPathname(), $filename);
        }
    }

    /**
     * Remove a directory and its content.
     */
    protected function rmdir(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $directory = new IteratorIterator(new DirectoryIterator($path));

        /** @var SplFileInfo */
        foreach ($directory as $file) {
            if ($file->isDir()) {
                continue;
            }

            unlink($file->getPathname());
        }

        rmdir($path);
    }
}
