<?php

namespace App\Exceptions;

use Exception;

class SucursalCannotBeDeletedException extends Exception
{
    public function __construct(string $message = "Esta sucursal no puede ser eliminada debido a reglas de negocio.", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
