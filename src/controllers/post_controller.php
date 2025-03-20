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
        $search = $body['search'] ?? '';

        $post_service = Post_service::fetch($authorization,$search);

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
            'jwt' => $post_service
        ], 200);
        return;
    }

    public function fetch_tag(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $search = $body['search'] ?? '';

        $post_service = Post_service::fetch_tag($authorization,$search);

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
            'jwt' => $post_service
        ], 200);
        return;
    }

    public function update(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $post_service = Post_service::update($authorization, $body);

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
            'message' => $post_service
        ], 200);
        return;
    }

    public function delete(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $post_service = Post_service::delete($authorization,$body);

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
            'message' => $post_service
        ], 200);
        return;
    }


}