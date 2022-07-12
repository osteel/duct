<?php

namespace Osteel\Duct\Services\Operator;

use Osteel\Duct\Services\Reporter;
use Osteel\Duct\Sieves\Sieve;
use Osteel\Duct\ValueObjects\Directory;
use Osteel\Duct\ValueObjects\Treatment;
use SplFileInfo;
use Throwable;

final class Operator
{
    public function __construct(private readonly Reporter $reporter)
    {
    }

    public function apply(Treatment $treatment, Directory $directory): void
    {
        $treatment->sieves->each(function (Sieve $sieve) use ($directory) {
            $class = explode('\\', $sieve::class);

            $this->reporter->action(sprintf('Current sieve: %s', array_pop($class)));

            $screened = ($screen = $sieve->getScreen()) ? new Screen($directory->iterator, $screen) : $directory->iterator;

            if (($count = iterator_count($screened)) === 0) {
                return $this->report($count);
            }

            $errors = [];

            $this->reporter->progressStart($count);
            $screened->rewind();

            /** @var SplFileInfo */
            foreach ($screened as $file) {
                try {
                    $sieve->getProcess()($file);
                } catch (Throwable $exception) {
                    $errors[] = [$file->getPathname(), $exception->getMessage()];
                }

                $this->reporter->progressAdvance();
            }

            $this->reporter->progressFinish();

            $this->report($count, $errors);
        });
    }

    private function report(int $total, array $errors = []): void
    {
        if ($total === 0) {
            $this->reporter->comment('No file to process!');
        } else {
            $this->reporter->success(sprintf('%s files processed!', $total));
        }

        if (empty($errors)) {
            return;
        }

        $this->reporter->warning(sprintf('There were %s errors:', count($errors)));

        $this->reporter->table(['File', 'Error'], $errors);
    }
}
