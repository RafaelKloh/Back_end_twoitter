<?php
use App\Http\Route;


//Rotas de usuario
Route::post('/users/create', 'user_controller@store');
Route::post('/users/login', 'user_controller@login');
Route::post('/users/fetch', 'user_controller@fetch');
Route::get('/users/get_info', 'user_controller@get_info');
Route::put('/users/update', 'user_controller@update');
Route::delete('/users/delete', 'user_controller@remove');
Route::post('/users/register_follower', 'user_controller@register_follower');
Route::post('/users/upload_profile', 'User_controller@upload_profile_image');
Route::post('/users/register_bio', 'User_controller@register_bio');


//Rotas de validação
Route::post('/users/verify_profile_picture', 'User_controller@verifyProfile');
Route::post('/users/verify_email', 'User_controller@verifyEmail');
Route::post('/users/fetch_verify_email', 'User_controller@fetch_verify_email');

//Rotas de Post
Route::post('/post/create', 'post_controller@create');
Route::get('/post/for_you', 'post_controller@for_you');
Route::post('/post/fetch', 'post_controller@fetch');
Route::post('/post/fetch_tag', 'post_controller@fetch_tag');
Route::put('/post/update', 'post_controller@update');
Route::delete('/post/delete', 'post_controller@delete');
Route::post('/post/create_comment', 'post_controller@create_comment');
Route::post('/post/register_like', 'post_controller@register_like');



