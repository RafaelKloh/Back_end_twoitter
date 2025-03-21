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
            
            $fields['tags'] = isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : [];

            $post = Post_model::create($user_from_JWT['id'], $fields);

    
            if (!$post) {
                return ['error' => 'Sorry, we could not create your post.'];
            }
    
            return "Post created successfully!";
        } 
        catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
            return ['error' => $e->getCode()];
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function fetch(mixed $authorization, string $search)
    {
        try {

            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'Please, login to access this resource.'];
            if($search === "") return ['error' => 'Please enter a valid text to search'];

            $post = Post_model::find($search);

            if(!$post) return ['error' => 'Sorry, we could not find a post with this text.'];

            return $post;
        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function fetch_tag(mixed $authorization, string $search)
    {
        try {
            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'Please, login to access this resource.'];
            if($search === "") return ['error' => 'Please enter a valid tag to search'];

            $post = Post_model::find_tag($search);

            if(!$post) return ['error' => 'Sorry, we could not find a post with this tag.'];

            return $post;
        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function update(mixed $authorization, array $data)
    {
        try {
            if(isset($authorization['error'])) return ['error' => $authorization['error']];

            $user_from_JWT = JWT::verify($authorization);

            if (!$user_from_JWT) return ['error' => 'Please login to access this resource.'];

            $post = Post_model::update($data);
            

            if(!$post) return ['error' => 'Sorry, we could not update your post.'];

            return "Post update succesfully.";

        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
    
    public static function delete(mixed $authorization, array $data)
    {
        try {
            if(isset($authorization['error'])) return ['error' => $authorization['error']];

            $user_from_JWT = JWT::verify($authorization);

            if (!$user_from_JWT) return ['error' => 'Please login to access this resource.'];

            $post = Post_model::delete($data);


            if(!$post) return ['error' => 'Sorry, we could not update your account.'];

            return "Post deleted succesfully.";

        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function create_comment(mixed $authorization,array $data)
    {
        try {
            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'Please, login to access this resource.'];

            $fields = Validator::validate([
                'post_id' => $data['post_id'] ?? '',
                'comment' => $data['comment'] ?? '',
                'commented_at' => $data['commented_at'] ?? '',
                'user_post_commented_id' => $data['user_post_commented_id'] ?? ''
            ]);
            
            $post = Post_model::create_comment($user_from_JWT['id'], $fields);

    
            if (!$post) {
                return ['error' => 'Sorry, we could not register your comment.'];
            }
    
            return "Comment registered successfully!";
        } 
        catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
            return ['error' => $e->getCode()];
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function register_like(mixed $authorization,array $data)
    {
        try {
            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'Please, login to access this resource.'];

            
            $fields = Validator::validate([
                'post_id' => $data['post_id'] ?? '',
                'liked_at' => $data['liked_at'] ?? '',
                'user_post_liked_id' => $data['user_post_liked_id'] ?? ''
            ]);
            
            $post = Post_model::register_like($user_from_JWT['id'], $fields);

            
            if (!$post) {
                return ['error' => 'Sorry, we could not register your like.'];
            }
    
            return "Like registered successfully!";
        } 
        catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
            return ['error' => $e->getCode()];
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}