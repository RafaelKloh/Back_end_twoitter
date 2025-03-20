<?php
use App\Http\Route;


//Rotas de usuario
Route::post('/users/create', 'user_controller@store');
Route::post('/users/login', 'user_controller@login');
Route::post('/users/fetch', 'user_controller@fetch');
Route::put('/users/update', 'user_controller@update');
Route::delete('/users/delete', 'user_controller@remove');

//Rotas de Post
Route::post('/post/create', 'post_controller@create');
Route::post('/post/fetch', 'post_controller@fetch');
Route::put('/post/update', 'post_controller@update');
Route::delete('/post/delete', 'post_controller@delete');

