<?php

namespace Osteel\Duct\Tests\Services;

use Osteel\Duct\Services\Operator\Operator;
use Osteel\Duct\Services\Reporter;
use Osteel\Duct\Tests\TestCase;
use Osteel\Duct\Tests\Traits\WorksWithDirectories;
use Osteel\Duct\ValueObjects\Directory;
use Osteel\Duct\ValueObjects\Treatment;

class OperatorTest extends TestCase
{
    use WorksWithDirectories;

    protected Operator $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = new Operator($this->createMock(Reporter::class));
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
        $treatment   = Treatment::make(['convert' => ['from' => $from, 'to' => $to]]);
        $source      = sprintf('%s/../../data/img/%s', __DIR__, $from);
        $destination = sprintf('%s/../../data/tmp/convert', __DIR__);

        $this->cpdir($source, $destination);

        $directory = Directory::make($destination);

        $this->assertFilesAre($from, $directory);

        $this->operator->apply($treatment, $directory);

        $this->assertFilesAre($to, $directory);

        $this->rmdir($destination);
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
        $treatment   = Treatment::make(['rename' => ['types' => ['jpg'], 'pattern' => $exif . ($format ? ':' . $format : '')]]);
        $source      = sprintf('%s/../../data/img/jpg', __DIR__);
        $destination = sprintf('%s/../../data/tmp/rename', __DIR__);

        $this->cpdir($source, $destination);

        $directory = Directory::make($destination);

        $this->assertFilenamesAreNumbers($directory);

        $this->operator->apply($treatment, $directory);

        $this->assertFilenamesAreDates($directory, $format);

        $this->rmdir($destination);
    }

    public function testItHandlesPathConflicts()
    {
        $treatment   = Treatment::make(['rename' => ['types' => ['jpg'], 'pattern' => 'DATE_TIME_ORIGINAL:Y-m-d']]);
        $source      = sprintf('%s/../../data/img/jpg', __DIR__);
        $destination = sprintf('%s/../../data/tmp/rename', __DIR__);

        $this->cpdir($source, $destination);

        $directory = Directory::make($destination);

        $assert = function (string $path) {
            $this->assertTrue(is_file(sprintf('%s/2022-04-30.jpg', $path)));
            $this->assertTrue(is_file(sprintf('%s/2022-04-30 (1).jpg', $path)));
            $this->assertTrue(is_file(sprintf('%s/2022-04-30 (2).jpg', $path)));
            $this->assertTrue(is_file(sprintf('%s/2022-05-01.jpg', $path)));
        };

        $this->operator->apply($treatment, $directory);
        $assert($destination);

        // Applying the sieve again should produce the same result.
        $this->operator->apply($treatment, $directory);
        $assert($destination);

        $this->rmdir($destination);
    }
}
