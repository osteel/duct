<?php

namespace Osteel\Duct\Sieves;

use Closure;
use Intervention\Image\ImageManager;
use Osteel\Duct\Services\PathGenerator;
use SplFileInfo;

final class Convert extends Sieve
{
    private static array $extensionMap = [
        'heic' => ['heic', 'heif'],
        'heif' => ['heic', 'heif'],
        'jpeg' => ['jpg', 'jpeg'],
        'jpg'  => ['jpg', 'jpeg'],
    ];

    private readonly string $from;
    private readonly string $to;

    protected function setOptions(array $options = []): static
    {
        // @TODO check that the right options are provided and that the formats are supported
        $this->from = $options['from'];
        $this->to   = $options['to'];

        return $this;
    }

    // @TODO support multiple extensions
    public function getScreen(): Closure|null
    {
        $extensions = self::$extensionMap[$this->from] ?? [$this->from];

        return fn (SplFileInfo $file) => in_array(strtolower($file->getExtension()), $extensions);
    }

    public function getProcess(): Closure
    {
        $manager   = new ImageManager(['driver' => 'imagick']);
        $generator = new PathGenerator();

        return function (SplFileInfo $file) use ($manager, $generator) {
            $path = $generator->uniquePath($file->getPath(), pathinfo($file->getFilename(), PATHINFO_FILENAME), $this->to);

            // @TODO handle exceptions
            $manager->make($file->getPathname())->save($path);
            unlink($file->getPathname());
        };
    }
}
