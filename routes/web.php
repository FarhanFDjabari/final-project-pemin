<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');

    $router->post('/login', 'AuthController@login');
});

$router->group(['prefix' => 'books'], function () use ($router) {
    $router->get('/', 'BookController@index');

    $router->get('/{bookId}', 'BookController@getBookById');
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/{userId}', 'UserController@getUserById');

        $router->put('/{userId}', 'UserController@updateUser');

        $router->delete('/{userId}', 'UserController@deleteUser');
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->get('/', 'TransactionController@getTransactions');

        $router->get('/{transactionId}', 'TransactionController@getTransaction');
    });
});

$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', 'UserController@users');
    });

    $router->group(['prefix' => 'books'], function () use ($router) {
        $router->post('/', 'BookController@insertBook');

        $router->put('/{bookId}', 'BookController@updateBook');

        $router->delete('/{bookId}', 'BookController@deleteBook');
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->put('/{transactionId}', 'TransactionController@updateTransaction');
    });
});

$router->group(['middleware' => 'auth:user'], function () use ($router) {
    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->post('/', 'TransactionController@createTransaction');
    });
});
