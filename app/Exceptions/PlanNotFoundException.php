<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class PlanNotFoundException extends Exception
{
    public function __construct(string $message = 'The requested plan could not be found.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
