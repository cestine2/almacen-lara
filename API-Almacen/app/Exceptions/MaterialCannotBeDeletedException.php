<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class MaterialCannotBeDeletedException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = "Este material no puede ser eliminado debido a reglas de negocio.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
