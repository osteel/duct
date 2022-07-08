<?php

namespace Osteel\Duct\Sieves\Utils;

class PathGenerator
{
    /**
     * Generate a unique path.
     */
    public function uniquePath(string $directory, string $file, string $extension, ?string $current = null): string
    {
        $withoutExtension = sprintf('%s/%s', $directory, $file);
        $path             = sprintf('%s.%s', $withoutExtension, $extension);

        // Already the right path.
        if ($path === $current) {
            return $path;
        }

        $counter = 0;
        $attempt = $path;

        while (file_exists($attempt)) {
            $attempt = sprintf('%s (%s).%s', $withoutExtension, ++$counter, $extension);

            // Already the right path.
            if ($attempt === $current) {
                return $attempt;
            }
        }

        return $attempt;
    }
}
