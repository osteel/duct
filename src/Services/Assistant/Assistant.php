<?php

namespace Osteel\Duct\Services\Assistant;

use Osteel\Duct\Services\Assistant\Exceptions\PreparationError;
use Osteel\Duct\ValueObjects\Directory;
use Osteel\Duct\ValueObjects\Treatment;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class Assistant
{
    private readonly array $treatments;

    public function __construct(string $treatmentList)
    {
        try {
            $yaml = Yaml::parseFile($treatmentList);
        } catch (ParseException) {
            throw PreparationError::invalidTreatments();
        }

        if (! is_array($yaml) || empty($yaml['treatments']) || ! is_array($yaml['treatments'])) {
            throw PreparationError::invalidTreatments();
        }

        $this->treatments = $yaml['treatments'];
    }

    public function open(string $path, bool $recursive): Directory
    {
        return Directory::make($path, $recursive);
    }

    public function prepare(string $treatment): Treatment
    {
        if (empty($this->treatments[$treatment])) {
            throw PreparationError::missingTreatment($treatment);
        }

        return Treatment::make($this->treatments[$treatment]);
    }
}
