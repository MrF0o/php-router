<?php

use Mrfoo\PHPRouter\Router;

function route($name, ...$params)
{
    return Router::generateURL($name, ...$params);
}

function getBaseUrl()
{
    $hostName = $_SERVER['HTTP_HOST'];
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';

    return $protocol . '://' . $hostName . "/";
}

function dd($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die;
}
