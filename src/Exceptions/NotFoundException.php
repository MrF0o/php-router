<?php

namespace Mrfoo\PHPRouter\Exceptions;

use Exception;

class NotFoundException extends Exception {

    public function __construct($uri)
    {
        $this->message = "route " . $uri . " doesn't exists.";
    }

    public function __toString(): string
    {
        return "MethodNotSupportedException: " . $this->message;
    }

}