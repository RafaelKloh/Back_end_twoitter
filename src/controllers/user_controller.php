<?php

namespace App\Controllers;

use App\Http\Request;  // Importando a classe Request corretamente
use App\Http\Response;
use App\Services\User_service;

class User_controller
{
    
    public function store(Request $request, Response $response)
{
    $body = $request::body();
    $profile_img = 'default.png'; // Imagem padrão

    if (!empty($_FILES['profile_picture']['name'])) {
        $upload_dir = __DIR__ . '/../../public/uploads/profile_pictures/';
        
        // Criar a pasta se não existir
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
        $upload_path = $upload_dir . $file_name;

        // Mover o arquivo para a pasta de uploads
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            $profile_img = $file_name; // Atualiza com o nome do arquivo salvo
        }
    }

    // Adiciona o nome da imagem ao corpo da requisição
    $body['profile_img'] = $profile_img;
    $user_services = User_service::create($body);

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
        'data' => $user_services
    ], 201);
}
public function upload_profile_image(Request $request, Response $response, array $args)
{
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../public/uploads/profile_pictures/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); 
        }

        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = uniqid() . "_" . basename($_FILES['profile_picture']['name']);
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            $image_url = $file_name;

            $user_id = $_POST['user_id'] ?? null; 
            $bio = $_POST['bio'] ?? null;

            if ($user_id) {
                $update_result = User_service::update_profile_image([
                    'user_id' => $user_id,
                    'profile_picture' => $image_url,
                    'bio' => $bio
                ]);
                if ($update_result['success']) {
                    return $response->json([
                        'error' => false,
                        'success' => true,
                        'message' => 'Imagem de perfil atualizada com sucesso!',
                        'image_url' => $image_url
                    ]);
                } else {
                    return $response->json([
                        'error' => true,
                        'success' => false,
                        'message' => $update_result['error']
                    ]);
                }
    }
}
    }
}










    public function verifyEmail(Request $request)
{
    header('Content-Type: application/json');
    echo json_encode(User_service::verifyEmail($request->all()));
    exit;
}





    public function verifyProfile(Request $request)
{
    $authorization = $request::authorization();
    $body = $request::body();
        $user_services = User_service::verifyProfileById($authorization, $body);

        if(isset($user_services['error'])){
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $user_services['error']
            ],400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'jwt' => $user_services
        ], 200);
        return;
}


public function register_bio(Request $request, Response $response)
{
    $body = $request::body();
        $user_services = User_service::register_bio($body);

        if(isset($user_services['error'])){
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $user_services['error']
            ],400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'jwt' => $user_services
        ], 200);
        return;
}

    
public function fetch_verify_email(Request $request, Response $response)
{
    $body = $request::body();
    $user_services = User_service::fetch_verify_email($body);

    if(isset($user_services['error'])){
        return $response::json([
            'error' => true,
            'success' => false,
            'message' => $user_services['error']
        ],400);
    }

    $response::json([
        'error' => false,
        'success' => true,
        'jwt' => $user_services
    ], 200);
    return;
}


    public function login(Request $request, Response $response)
    {
        
        $body = $request::body();
        $user_services = User_service::auth($body);

        if(isset($user_services['error'])){
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $user_services['error']
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
                'message' => $user_services['error']
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
                'message' => $user_services['error']
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
                'message' => $user_services['error']
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
                'message' => $user_services['error']
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
                'message' => $user_services['error']
            ],400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'data' => $user_services
        ], 201);
    }
}