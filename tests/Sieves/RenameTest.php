<?php

namespace Osteel\Duct\Tests\Sieves;

use DateTime;
use Osteel\Duct\Sieves\Rename;
use Osteel\Duct\ValueObjects\Directory;
use SplFileInfo;

class RenameTest extends TestCase
{
    private function assertFilenamesAreNumbers(Directory $directory): void
    {
        /** @var SplFileInfo */
        foreach ($directory->iterator as $file) {
            if ($file->isFile()) {
                $this->assertEquals(1, preg_match('/^0\d$/', pathinfo($file->getFilename(), PATHINFO_FILENAME)));
            }
        }
    }

    private function assertFilenamesAreDates(Directory $directory, ?string $format = null): void
    {
        /** @var SplFileInfo */
        foreach ($directory->iterator as $file) {
            if ($file->isFile()) {
                $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $this->assertEquals($filename, DateTime::createFromFormat($format, $filename)->format($format));
            }
        }
    }

    public function patternProvider(): array
    {
        return [
            ['DATE_TIME_ORIGINAL', 'Y-m-d H-i-s'],
        ];
    }

    /**
     * @dataProvider patternProvider
     */
    public function testItRenamesTheFiles(string $exif, ?string $format = null)
    {
        $sieve       = new Rename(['types' => ['jpg'], 'pattern' => $exif . ($format ? ':' . $format : '')]);
        $source      = sprintf('%s/../data/img/jpg', __DIR__);
        $destination = sprintf('%s/../data/rename', __DIR__);

        $this->cpdir($source, $destination);

        $directory = Directory::make($destination);

        $this->assertFilenamesAreNumbers($directory);

        $sieve->filter($directory);

        $this->assertFilenamesAreDates($directory, $format);

        $this->rmdir($destination);
    }
}
