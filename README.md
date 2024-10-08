# php-router

This router is designed to help you easily and efficiently handle HTTP requests and responses in your PHP applications.
It is inspired by the popular Laravel framework, and aims to provide a similar experience and functionality.

# Getting Started

- [Installation](#installation)
- [Setup](#setup)
- [The Router class](#the-router-class)
- [Route parameters](#route-parameters)
- [Regular Expression Constraints](#regular-expression-constraints)
- [Named Routes](#named-routes)
- [Grouping routes](#grouping-routes)
- [Route redirects](#route-redirects)
- [Generating URLs](#generating-urls)
- [Middlewares](#middlewares)
- [Example](#quick-example)
- [TODO](#todo)

## Installation

You may use composer to intall [MrF0o/php-router](https://github.com/MrF0o/php-router) by running this command:

```bash
composer require mrf0o/php-router
```

## Setup
After installing the package you may run your app either via the php command line or via a reverse-proxy server such as Apache.

#### Method 1: PHP CLI
create an `index.php` for example in the root of your project (you can name whatever you want) with this content:

```php
<?php

include_once "vendor/autoload.php";

use Mrfoo\PHPRouter\Router;

Router::get('/', function() {
    echo 'Hello World!';
});

Router::run();
```

```shell
php -S localhost:8888 index.php
```
This command will run your app on port 8888. And if everything went correctly, visiting http://localhost:8888 on your browser should show the text 'Hello World'.

#### Method 2: Reverse proxy
Here you can find a .htaccess file example that you can use with the rewrite rules needed for this router to run correctly. You can use the same example code from the previous method and make sure to place the file somewhere under the document root that Apache expects (Generally htdocs).

> Apache by default uses index.php as the main file so it will serve it by default. but if you want to use this router globally within you project you want all traffic to point to the index.php even tho the user tried to access a different folder withing you project.

```apacheconf
<IfModule mod_rewrite.c>
<IfModule mod_negotiation.c>
    Options -MultiViews
</IfModule>

RewriteEngine On

RewriteCond %{REQUEST_FILENAME} -d [OR]
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^ ^$1 [N]

RewriteCond %{REQUEST_URI} (\.\w+$) [NC]
RewriteRule ^(.*)$ public/$1

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php
</IfModule>
```
Also, notice the public/ directory in the rewrite rule. This rule redirects all asset requests to the public folder. You can rename this folder to something else, like assets, if you'd prefer. Just make sure to update the rewrite rule accordingly.

Note: you need to change index.php to the file that contains the Router::run() call.

> [!IMPORTANT]
> This router does not support subfolders yet. You may need extra configurations in your .htaccess to make it work.

## The Router class

After the installation is complete you can start using the router by including The `Router` class. for example this
route will fire whenever `/hello` is hit:

```php
use Mrfoo\PHPRouter\Router;

Router::get('/hello', function () {
    echo  '<h1>hello there</h1>';
});

Router::run();
```

You can see that each request is represented as a static method of the Router class, thus you can change the `get` with
each of these methods: `post`, `put`, `patch` and `delete`.

Make sure to run the Router using the `run` static method.

## Route parameters

To capture a segment in your url you can use route parameters, these parameters will be passed to the handler function
in order.

```php
Router::get('/hello/{name}', function ($name) {
    echo  '<h1>hello '.$name.'</h1>';
});
```

> optional parameters aren't implemented yet, so this route will be matched only if the parameter {name} is present,
> otherwise it will give a 404 error

## Regular Expression Constraints

Sometimes, you may need to constrain a parameter using regular expression, you can do this using the `where` method on
the Route instance.

```php
Router::get('/user/{name}', function ($name) {
    // ...
})->where('name', '[A-Za-z]+');

Router::get('/user/{id}/{name}', function  (string  $id, string  $name) {
    // ...
})->where(['id'  =>  '[0-9]+', 'name'  =>  '[a-z]+']);
```

## Named Routes

You may give names to your routes using the `name` method on Route instance, this will make it easier to reference your
routes elsewhere in your code using the `route` helper method later.

```php
Router::get('/user/profile', function  () {
    // ...
})->name('profile');
```

## Grouping routes

Also, you can create route groups using the `group` method of the router class, each Route registered in the callback
will share the properties passed to the `group` method.

```php
Router::group(['prefix' => '/user'], function () {
    // here all routes will be prefixed with /user
    Router::get('/update', fn () => die('not implemented')); /* /user/update */
});
```

## Route redirects

You can redirect a route to another route using the `redirect` method on the Router class.

```php
Router::redirect('/old', '/new');
```

by default the redirect will be a `302` redirect, but you can change that by passing the status code as the third
argument.

```php
Router::redirect('/old', '/new', 301);
```

or if you would like a permanent redirect you can use the `permanentRedirect` method, this will send a `301` redirect.

```php
Router::permanentRedirect('/old', '/new');
```

## Generating URLs

You can generate URLs for your routes using the `route` helper method, this method accepts the name of the route and a
variable count of parameters to be passed to the route.

```php
Router::get('/user/{id}', function ($id) {
    // ...
})->name('user.profile');

$url = route('user.profile', 1);
// $url = 'http://example.com/user/1'
```

# Middlewares

Middlewares are a great way to filter requests before they reach your route handler, in this router library Middlewares
are represented as classes that Overrides the `handle` method in the \Mrfoo\PHPRouter\Middleware class.

```php
<?php
namespace Middlewares;

use Mrfoo\PHPRouter\Core\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            // redirect to login page
            header('Location: /login');
            exit;
        }
    }
}
```

the handle method will be called before the route handler, so you can do any checks you want and redirect the user if
needed.

Then you can use the middleware in your routes like this:

```php
Router::get('/user/profile', function () {
    // ...
})->name('user.profile')->middleware(AuthMiddleware::class);
```

and you may assign multiple middlewares to a route like this:

```php
Router::get('/user/profile', function () {
    // ...
})->name('user.profile')->middleware([AuthMiddleware::class, AnotherMiddleware::class]);
```

you can override the `terminate` method to do any cleanup after the route handler is called.

```php
<?php
namespace Middlewares;

use Mrfoo\PHPRouter\Core\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            // redirect to login page
            header('Location: /login');
            exit;
        }
    }

    public function terminate()
    {
        // do some cleanup
    }
}
```

## Quick Example

```php
<?php
include './vendor/autoload.php';
use Mrfoo\PHPRouter\Router;
use Controllers\UserController;

Router::get('/greet', function () {
    echo 'hello everyone';
})->name('greeting');

Router::post('/user/create', function () {
    // ...
    $id = $_POST['user_id']; // make sure to sanitize your inputs!
})->name('user.create');

Router::patch('/user/profile', [UserController::class, 'update'])->name('user.profile.update');

// make it bun dem!
Router::run();
```

Here I used `UserController` class as an example to demonstrate, the other convention to use route handlers besides the
callback function.

# TODO

- [X] Route Grouping
- [X] Route redirects
- [X] `route` helper function, this should be globally available
- [X] helper methods for routes constraints
- [X] Middlewares
- [ ] Rate limiting
