<?php

namespace Osteel\Duct\Sieves;

use DateTime;
use Exception;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Osteel\Duct\Services\Interpreter;
use Osteel\Duct\Sieves\Utils\ExtensionFilter;
use Osteel\Duct\Sieves\Utils\PathGenerator;
use Osteel\Duct\ValueObjects\Directory;
use SplFileInfo;

class Rename extends Sieve
{
    private array $types;
    private string $pattern;

    public function __construct(private Interpreter $interpreter, array $options)
    {
        // @TODO check that the right options are provided and that the formats are supported
        $this->types   = $options['types'];
        $this->pattern = $options['pattern'];
    }

    public function filter(Directory $directory): int
    {
        $filtered  = new ExtensionFilter($directory->iterator, $this->types);
        $manager   = new ImageManager(['driver' => 'imagick']);
        $generator = new PathGenerator();

        if (($count = iterator_count($filtered)) === 0) {
            return $count;
        }

        $this->interpreter->progressStart($count);
        $filtered->rewind();

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

            $path = $generator->uniquePath($file->getPath(), $filename, $file->getExtension(), $file->getPathname());

            // @TODO handle exceptions
            rename($file->getPathname(), $path);

            $this->interpreter->progressAdvance();
        }

        $this->interpreter->progressFinish();

        return $count;
    }
}
