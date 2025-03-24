<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\User_service;

class User_controller
{
    
    public function store(Request $request, Response $response)
    {
        
        $body = $request::body();
        $user_services = User_service::create($body);

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
            'data' => $user_services
        ], 201);
    }

    public function verifyEmail(Request $request)
{
    header('Content-Type: application/json');
    echo json_encode(User_service::verifyEmail($request->all()));
    exit;
}

    
    


    public function login(Request $request, Response $response)
    {
        
        $body = $request::body();
        $user_services = User_service::auth($body);

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

    public function update(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $user_services = User_service::update($authorization, $body);

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
            'message' => $user_services
        ], 200);
        return;
    }

    public function remove(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $user_services = User_service::delete($authorization);

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
            'message' => $user_services
        ], 200);
        return;
    }

    public function register_follower(Request $request, Response $response)
    {
        $body = $request::body();
        $authorization = $request::authorization();
        $user_services = User_service::register_follower($authorization,$body);

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
            'data' => $user_services
        ], 201);
    }

    public function get_info(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $user_services = User_service::get_info($authorization);

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
            'data' => $user_services
        ], 201);
    }
}