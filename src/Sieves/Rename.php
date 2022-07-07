<?php

namespace Osteel\Duct\Sieves;

use DateTime;
use Exception;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Osteel\Duct\Sieves\Utils\ExtensionFilter;
use Osteel\Duct\ValueObjects\Directory;
use SplFileInfo;

class Rename extends Sieve
{
    private array $types;
    private string $pattern;

    public function __construct(array $options)
    {
        // @TODO check that the right options are provided and that the formats are supported
        $this->types   = $options['types'];
        $this->pattern = $options['pattern'];
    }

    public function filter(Directory $directory): void
    {
        $filtered = new ExtensionFilter($directory->iterator, $this->types);
        $manager  = new ImageManager(['driver' => 'imagick']);

        // @TODO handle this better
        if (preg_match('/^(\w*):?(.*)$/', $this->pattern, $matches) === false) {
            throw new Exception('Invalid format');
        }

        [, $exif, $format] = $matches;

        /** @var SplFileInfo */
        foreach ($filtered as $file) {
            // @TODO handle this better
            if (empty($filename = $manager->make($file->getPathname())->exif(Str::studly(strtolower($exif))))) {
                throw new Exception('Exif not available');
            }

            // @TODO handle exceptions
            if ($format) {
                $filename = (new DateTime($filename))->format($format);
            }

            // @TODO handle exceptions
            rename($file->getPathname(), $this->generateUniquePath($file, $filename));
        }
    }

    private function generateUniquePath(SplFileInfo $file, string $filename): string
    {
        $withoutExtension = sprintf('%s/%s', $file->getPath(), $filename);
        $path             = sprintf('%s.%s', $withoutExtension, $file->getExtension());

        // Already the right path.
        if ($path === $file->getPathname()) {
            return $path;
        }

        $counter = 0;
        $attempt = $path;

        while (file_exists($attempt)) {
            $attempt = sprintf('%s (%s).%s', $withoutExtension, ++$counter, $file->getExtension());
            // Already the right path.
            if ($attempt === $file->getPathname()) {
                return $attempt;
            }
        }

        return $attempt;
    }
}
