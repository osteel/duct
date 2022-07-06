<?php

namespace Osteel\Duct\Tests\Sieves;

use Osteel\Duct\Sieves\Convert;
use Osteel\Duct\ValueObjects\Directory;
use SplFileInfo;

class ConvertTest extends TestCase
{
    private function assertFilesAre(string $extension, Directory $directory): void
    {
        $extension = strtolower($extension);

        /** @var SplFileInfo */
        foreach ($directory->iterator as $file) {
            if ($file->isFile()) {
                $this->assertEquals($extension, strtolower($file->getExtension()));
            }
        }
    }

    public function extensionsProvider(): array
    {
        return [
            ['jpg', 'png'],
            ['jpg', 'jpeg'],
        ];
    }

    /**
     * @dataProvider extensionsProvider
     */
    public function testItConvertsTheFiles(string $from, string $to)
    {
        $sieve       = new Convert(['from' => $from, 'to' => $to]);
        $source      = sprintf('%s/../data/img/%s', __DIR__, $from);
        $destination = sprintf('%s/../data/convert', __DIR__);

        $this->cpdir($source, $destination);

        $directory = Directory::make($destination);

        $this->assertFilesAre($from, $directory);

        $sieve->filter($directory);

        $this->assertFilesAre($to, $directory);

        $this->rmdir($destination);
    }
}
