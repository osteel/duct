<?php

namespace Osteel\Duct\Services\Configurator\Exceptions;

use Exception;
use Throwable;

class MissingConfiguration extends Exception
{
    public function __construct(
        string $message = 'Missing configuration',
        int $code = 0,
        Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
