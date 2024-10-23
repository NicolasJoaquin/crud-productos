<?php

namespace App\Exceptions;

use Exception;

class JsonWriteException extends Exception
{
    protected $message = 'Error al escribir en el archivo JSON.';
    protected $code = 500;

    public function __construct($message = null, $code = null)
    {
        parent::__construct($message ?? $this->message, $code ?? $this->code);
    }
}
