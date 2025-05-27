<?php

namespace App\Exceptions;

use Exception; // O use RuntimeException;
use Throwable; // Importa Throwable para el constructor

class ProveedorCannotBeDeletedException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "Este proveedor no puede ser eliminado debido a reglas de negocio.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
