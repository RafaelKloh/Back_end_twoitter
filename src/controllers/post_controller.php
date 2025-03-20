<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\Post_service;

class Post_controller
{
    
    public function create(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $post_service = Post_service::create($authorization,$body);

        if(isset($post_service['error'])){
            return $response::json([
                'error' => true,
                'success' => false,
                'mesage' => $post_service['error']
            ],400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'data' => $post_service
        ], 201);
    }

    public function fetch(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $name = $body['name'] ?? '';

        $user_services = User_service::fetch($authorization,$name);

        if(isset($user_services['error'])){
            return $response::json([
                'error' => true,
                'success' => false,
                'mesage' => $user_services['error']
            ],400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'jwt' => $user_services
        ], 200);
        return;
    }

}