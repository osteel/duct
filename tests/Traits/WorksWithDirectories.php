<?php

namespace Osteel\Duct\Tests\Traits;

use DateTime;
use DirectoryIterator;
use IteratorIterator;
use Osteel\Duct\ValueObjects\Directory;
use SplFileInfo;

trait WorksWithDirectories
{
    protected function assertFilesAre(string $extension, Directory $directory): void
    {
        $extension = strtolower($extension);

        /** @var SplFileInfo */
        foreach ($directory->iterator as $file) {
            if ($file->isFile()) {
                $this->assertEquals($extension, strtolower($file->getExtension()));
            }
        }
    }

    protected function assertFilenamesAreNumbers(Directory $directory): void
    {
        /** @var SplFileInfo */
        foreach ($directory->iterator as $file) {
            if ($file->isFile()) {
                $this->assertEquals(1, preg_match('/^0\d$/', pathinfo($file->getFilename(), PATHINFO_FILENAME)));
            }
        }
    }

    protected function assertFilenamesAreDates(Directory $directory, ?string $format = null): void
    {
        /** @var SplFileInfo */
        foreach ($directory->iterator as $file) {
            if ($file->isFile()) {
                $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $this->assertEquals($filename, DateTime::createFromFormat($format, $filename)->format($format));
            }
        }
    }

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
     * Delete a directory and its content.
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
