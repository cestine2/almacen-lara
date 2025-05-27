<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InventarioCannotBeDeletedException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = "Este registro de inventario no puede ser eliminado debido a reglas de negocio.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
