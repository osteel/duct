<?php

namespace Osteel\Duct\Tests\Sieves\Utils;

use Osteel\Duct\Sieves\Utils\PathGenerator;
use Osteel\Duct\Tests\TestCase;

class PathGeneratorTest extends TestCase
{
    private PathGenerator $generator;
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new PathGenerator();
        $this->directory = sprintf('%s/../../data/img/jpg', __DIR__);
    }

    public function fileProvider(): array
    {
        return [
            ['01', null, '01 (1)'],
            ['02', null, '02 (1)'],
            ['01', '01', '01'],
        ];
    }

    /**
     * @dataProvider fileProvider
     */
    public function testItGeneratesAUniquePath(string $file, ?string $current, string $result)
    {
        $current = $current ? sprintf('%s/%s.jpg', $this->directory, $current) : null;
        $path    = $this->generator->uniquePath($this->directory, $file, 'jpg', $current);

        $this->assertEquals(sprintf('%s/%s.jpg', $this->directory, $result), $path);
    }
}
