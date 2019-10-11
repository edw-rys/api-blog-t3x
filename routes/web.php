<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Middleware\ApiAuthMiddleware;  


Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', 'PruebaController@testORM');
Route::get('/user', 'UserController@test');

Route::get('/posts', 'UserController@test');
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{id}', 'UserController@detail');

Route::post('/api/signup', 'UserController@signup');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);


// Routas del controlador de categorias
Route::resource('/api/category', 'CategoryController');

// Rutas del controlador de entrada
Route::resource('/api/post'  , 'PostController');
Route::post('/api/post/upload', 'PostController@upload');
Route::post('/api/post/upload', 'PostController@upload');
Route::get('/api/post/img/{filename}', 'PostController@getImage');
Route::get('/api/post/category/{id}', 'PostController@getPostByCategory');
Route::get('/api/post/user/{id}', 'PostController@getPostByUser');
