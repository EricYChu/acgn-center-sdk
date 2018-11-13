<?php

namespace Acgn\Center\Exceptions;

class Exception extends \Exception
{
    protected $errors = [];

    public function __construct(string $message, int $code = 0, array $errors = [])
    {
        parent::__construct($message, $code);

        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
