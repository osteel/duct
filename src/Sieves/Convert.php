<?php

namespace Osteel\Duct\Sieves;

use Exception;
use Intervention\Image\ImageManager;
use Osteel\Duct\Sieves\Utils\ExtensionFilter;
use Osteel\Duct\ValueObjects\Directory;
use SplFileInfo;

class Convert extends Sieve
{
    private string $from;
    private string $to;

    public function __construct(array $options)
    {
        // @TODO check that the right options are provided and that the formats are supported
        $this->from = $options['from'];
        $this->to   = $options['to'];
    }

    public function filter(Directory $directory): void
    {
        $filtered = new ExtensionFilter($directory->iterator, [$this->from]);
        $manager  = new ImageManager(['driver' => 'imagick']);

        /** @var SplFileInfo */
        foreach ($filtered as $file) {
            $path = sprintf('%s/%s.%s', $file->getPath(), pathinfo($file->getFilename(), PATHINFO_FILENAME), $this->to);

            // @TODO handle this better
            if (file_exists($path)) {
                throw new Exception('File already exists');
            }

            // @TODO handle exceptions
            $manager->make($file->getPathname())->save($path);
            unlink($file->getPathname());
        }
    }
}
