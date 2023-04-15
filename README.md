# php-router

(WORK IN PROGRESS)

This router is designed to help you easily and efficiently handle HTTP requests and responses in your PHP applications. It is inspired by the popular Laravel framework, and aims to provide a similar experience and functionality.

# Features

This router has several features that make it a powerful tool for handling HTTP requests and responses:

- Simple, intuitive syntax inspired by Laravel's routing system
- Ability to handle GET, POST, PUT, DELETE, and other HTTP request methods
- Support for dynamic route parameters, which can be used to capture variable parts of URLs
- Automatic generation of named routes, which can be used to easily generate URLs for specific routes
- Middleware support, which allows you to add additional processing logic to your routes
- Ability to group routes together and apply middleware to groups
- Extensible architecture that allows you to easily add your own functionality and customize the router to your needs


# Getting Started
## Installation
You may use composer to intall [MrF0o/php-router](https://github.com/MrF0o/php-router) by running this command:
```bash
composer require mrf0o/php-router
```

## The Router class
After the installation is complete you can start using the router by including The `Router` class. for example this route will fire whenever `/hello` is hit:
```php
use Mrfoo\PHPRouter\Router;

Router::get('/hello', function () {
	echo  '<h1>hello there</h1>';
});

Router::run();
```

You can see that each request is represented as a static method of the Router class, thus you can change the `get` with each of these methods: `post`, `put`, `patch` and `delete`.

Make sure to run the Router using the `run` static method.

## Route parameters
To capture a segment in your url you can use route parameters, these parameters will be passed to the handler function in order.

```php
Router::get('/hello/{name}', function ($name) {
	echo  '<h1>hello '.$name.'</h1>';
})
```

> optional parameters aren't implemented yet, so this route will be matched only if the parameter {name} is present, otherwise it will give a 404 error

## Regular Expression Constraints
Sometimes, you may need to constrain a parameter using regular expression, you can do this using the `where` method on the Route instance.
```php
Router::get('/user/{name}', function ($name) {
	// ...
})->where('name', '[A-Za-z]+');

Router::get('/user/{id}/{name}', function  (string  $id, string  $name) {
	// ...
})->where(['id'  =>  '[0-9]+', 'name'  =>  '[a-z]+']);
```

## Named Routes
You may give names to your routes using the `name` method on Route instance, this will make it easier to reference your routes elsewhere in  your code using the `route` helper method later.

```php
Router::get('/user/profile', function  () {
	// ...
})->name('profile');
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

Here I used `UserController` class as an example to demonstrate, the other convention to use route handlers besides the callback function.

# TODO
- [ ] Route Grouping
- [ ] Route redirects
- [ ] `route` helper method, this should be globally available
- [ ] Middlewares
- [ ] Rate limiting
- [ ] helper methods for routes constraints