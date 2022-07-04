<?php

namespace Osteel\Duct\Sieves\Convert;

use DirectoryIterator;
use Intervention\Image\ImageManager;
use Osteel\Duct\Sieves\Sieve;
use RecursiveDirectoryIterator;

class Convert extends Sieve
{
    private string $from;
    private string $to;

    public function __construct(array $options)
    {
        // @TODO check that the right options are provided and that the formats are supported
        $this->from = $options['from'];
        $this->to 	= $options['to'];
    }

    public function process(DirectoryIterator | RecursiveDirectoryIterator $directory): void
    {
        $filtered = new Filter($directory, $this->from);
        $manager  = new ImageManager(['driver' => 'imagick']);

        /** @var DirectoryIterator */
        foreach ($filtered as $file) {
            // @TODO handle exceptions
            $manager->make($file->getPathname())
                ->save(sprintf('%s/%s.%s', $file->getPath(), pathinfo($file->getFilename(), PATHINFO_FILENAME), $this->to));
            unlink($file->getPathname());
        }

        if (! $directory instanceof RecursiveDirectoryIterator) {
            return;
        }

        foreach ($directory->getChildren() as $subdirectory) {
            $this->process($subdirectory);
        }
    }
}
