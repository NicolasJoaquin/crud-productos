<?php

namespace App\Exceptions;

use Exception;

class InvalidRequestException extends Exception
{
    protected $message = 'Datos inválidos en la solicitud.';
    protected $code = 400;

    public function __construct($message = null, $code = null)
    {
        parent::__construct($message ?? $this->message, $code ?? $this->code);
    }
}
