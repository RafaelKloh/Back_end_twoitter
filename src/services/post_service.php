<?php

namespace App\Services;

use Exception;
use PDOException;
use App\Models\Post_model;
use App\Utils\Validator;
use App\Http\JWT;

class Post_service
{
    public static function create(mixed $authorization,array $data)
    {
        try {
            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'Please, login to access this resource.'];

            $fields = Validator::validate([
                'text_post' => $data['text_post'] ?? '',
                'image_post' => $data['image_post'] ?? '',
                'posted_at' => $data['posted_at'] ?? ''
            ]);
    
            $post = Post_model::create($user_from_JWT['id'], $fields);

    
            if (!$post) {
                return ['error' => 'Sorry, we could not create your account.'];
            }
    
            return "Post created successfully!";
        } 
        catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
            if($e->getCode() === '23000') return ['error' => 'Sorry, user already exists.'];
            return ['error' => $e->getCode()];
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

}