<?php

namespace Osteel\Duct\Sieves;

use Closure;
use DateTime;
use Exception;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Osteel\Duct\Services\PathGenerator;
use SplFileInfo;
use Throwable;

class Rename extends Sieve
{
    private static array $extensionMap = [
        'heic' => ['heic', 'heif'],
        'heif' => ['heic', 'heif'],
        'jpeg' => ['jpg', 'jpeg'],
        'jpg'  => ['jpg', 'jpeg'],
    ];

    private readonly array $types;
    private readonly string $pattern;

    protected function setOptions(array $options = []): static
    {
        // @TODO check that the right options are provided and that the formats are supported
        $this->types   = $options['types'];
        $this->pattern = $options['pattern'];

        return $this;
    }

    public function getScreen(): Closure|null
    {
        $extensions = [];

        foreach ($this->types as $extension) {
            $extensions = array_merge($extensions, self::$extensionMap[$extension] ?? [$extension]);
        }

        return fn (SplFileInfo $file) => in_array(strtolower($file->getExtension()), $extensions);
    }

    public function getProcess(): Closure
    {
        $manager   = new ImageManager(['driver' => 'imagick']);
        $generator = new PathGenerator();

        // @TODO handle this better
        if (preg_match('/^(\w*):?(.*)$/', $this->pattern, $matches) === false) {
            throw new Exception('Invalid format');
        }

        [, $exif, $format] = $matches;

        // @TODO handle this better
        try {
            $format ? (new DateTime())->format($format) : null;
        } catch (Throwable) {
            throw new Exception('Invalid format');
        }

        return function (SplFileInfo $file) use ($manager, $generator, $exif, $format) {
            // @TODO handle this better
            if (empty($filename = $manager->make($file->getPathname())->exif(Str::studly(strtolower($exif))))) {
                return;
            }

            // @TODO handle exceptions
            if ($format) {
                $filename = (new DateTime($filename))->format($format);
            }

            $path = $generator->uniquePath($file->getPath(), $filename, $file->getExtension(), $file->getPathname());

            // @TODO handle exceptions
            rename($file->getPathname(), $path);
        };
    }
}
