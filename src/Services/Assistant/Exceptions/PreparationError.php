<?php

namespace Osteel\Duct\Services\Assistant\Exceptions;

use Exception;
use Throwable;

class PreparationError extends Exception
{
    private function __construct(string $message = '', int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidTreatments(): self
    {
        return new PreparationError('The treatment list is invalid. Make sure the yaml file is correctly formatted.');
    }

    public static function missingTreatment(string $treatment): self
    {
        return new PreparationError(sprintf('Missing treatment: %s.', $treatment));
    }
}
