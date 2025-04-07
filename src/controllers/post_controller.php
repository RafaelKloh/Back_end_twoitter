<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\Post_service;

class Post_controller
{

    public function create(Request $request, Response $response, array $args)
    {
        $authorization = $request::authorization();
        if (isset($_FILES['image_post']) && $_FILES['image_post']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/image_posts/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_tmp = $_FILES['image_post']['tmp_name'];
            $file_name = uniqid() . "_" . basename($_FILES['image_post']['name']);
            $upload_path = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_url = $file_name;


                $body = [
                    "tags" => $_POST['tags'] ?? null,
                    "text_post" => $_POST['text_post'] ?? null,
                    "posted_at" => $_POST['posted_at'] ?? null,
                    "image_post" => $image_url
                ];

                $post_service = Post_service::create(
                    $authorization,
                    $body
                );


                if (isset($post_service['error'])) {
                    return $response::json([
                        'error' => true,
                        'success' => false,
                        'message' => $post_service['error']
                    ], 400);
                }

                $response::json([
                    'error' => false,
                    'success' => true,
                    'jwt' => $post_service
                ], 200);
                return;
            }
        }
    }





    public function fetch(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $search = $body['search'] ?? '';

        $post_service = Post_service::fetch($authorization, $search);

        if (isset($post_service['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $post_service['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'jwt' => $post_service
        ], 200);
        return;
    }

    public function for_you(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $user_services = Post_service::for_you($authorization);

        if (isset($user_services['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $user_services['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'jwt' => $user_services
        ], 200);
        return;
    }

    public function fetch_tag(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $search = $body['search'] ?? '';

        $post_service = Post_service::fetch_tag($authorization, $search);

        if (isset($post_service['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $post_service['error']
            ], 400);
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

        if (isset($post_service['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $post_service['error']
            ], 400);
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
        $post_service = Post_service::delete($authorization, $body);

        if (isset($post_service['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $post_service['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'message' => $post_service
        ], 200);
        return;
    }

    public function create_comment(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $post_service = Post_service::create_comment($authorization, $body);

        if (isset($post_service['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $post_service['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'data' => $post_service
        ], 201);
    }

    public function register_like(Request $request, Response $response)
    {
        $authorization = $request::authorization();
        $body = $request::body();
        $post_service = Post_service::register_like($authorization, $body);

        if (isset($post_service['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $post_service['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'data' => $post_service
        ], 201);
    }
}
