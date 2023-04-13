<?php

// this file is just for testing purposes

include './vendor/autoload.php';

include './src/Router.php';

// this route is lovely, and it will greet any name!
Router::get('/hello/{name}', function ($name) {
    echo '<h1>hello <u>' . $name . '</u></h1>';
})->name('hello');

// run the router
Router::run();