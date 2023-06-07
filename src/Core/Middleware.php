<?php

namespace Mrfoo\PHPRouter\Core;

// a Base class for all middlewares
abstract class Middleware {
    
    // what's the point of a middleware when it doesn't do anything?
    abstract public function handle();

    public function terminate() {
        // silence is golden
    }
}