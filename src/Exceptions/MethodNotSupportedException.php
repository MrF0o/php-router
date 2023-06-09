<?php

namespace Mrfoo\PHPRouter\Exceptions;

use Exception;

class MethodNotSupportedException extends Exception {

    public function __construct($method, $supportedMethods = [])
    {
        $_s = implode(', ', $supportedMethods);
        $this->message = "Method $method is not supported for this route, supported methods $_s";
    }

    public function __toString(): string
    {
        return "MethodNotSupportedException: " . $this->message;
    }

}