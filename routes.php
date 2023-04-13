<?php

include './vendor/autoload.php';

include './src/Router.php';

Router::get('/hello/{name}', function ($name) {
    echo 'hello ' . $name;
})->name('hello');

Router::run();