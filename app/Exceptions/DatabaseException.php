<?php

namespace App\Exceptions;

use PDOException;

class DatabaseException extends PDOException
{
    protected $context;

    public function __construct($message, $code = 0, \Exception $previous = null, $context = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
        error_log('Database Exception: ' . $this->getCustomMessage());

    }

    public function getCustomMessage()
    {
        return "An error occurred: " . $this->getMessage()  . "Context: " . $this->getContext() . "\n";
    }

    public function getContext()
    {
        return $this->context;
    }
}
