<?php

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

$app->get('/', function () use ($app) {
    return view('master');
});


$app->get('/search', ['uses' => 'ImageResourceController@fetchImages']);
$app->post('/print', ['uses' => 'PrinterController@printImages']);
$app->get('/printget', ['uses' => 'PrinterController@printget']);