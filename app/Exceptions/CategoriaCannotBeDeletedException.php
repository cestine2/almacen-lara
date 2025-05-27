<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CategoriaCannotBeDeletedException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "Esta categoría no puede ser eliminada debido a reglas de negocio.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
