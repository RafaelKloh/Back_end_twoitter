<?php

namespace App\Services;

use Exception;
use PDOException;
use App\Models\User_model;
use App\Utils\Validator;
use App\Http\JWT;

class User_service
{
    public static function create(array $data)
    {
        try {
            $fields = Validator::validate([
                'name' => $data['name'] ?? '',
                'email' => $data['email'] ?? '',
                'password' => $data['password'] ?? '',
                'sex' => $data['sex'] ?? '',
                'user_birth_date' => $data['user_birth_date'] ?? '',
                'user_creation_date' => $data['user_creation_date'] ?? ''
            ]);
            $bio =$data['bio'];
            $profile_img = $data['profile_img'];

            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);


            //gera o codigo de verificação aleatório pra api
            $verification_code = rand(100000, 999999);
    
            $user = User_model::save($fields, $verification_code,$bio,$profile_img);
    
            if (!$user) {
                return ['error' => 'Sorry, we could not create your account.'];
            }

            Mail_service::sendVerificationEmail($fields['email'], $verification_code);
    
            return "User created successfully! Please check your email to verify your account.";
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


    
    public static function verifyEmail(array $data)
    {
        try {
            $fields = Validator::validate([
                'email' => $data['email'] ?? '',
                'verification_code' => $data['verification_code'] ?? ''
            ]);

            $user = User_model::findByEmail($fields['email']);

            if (!$user || !$user['verification_code'] || $user['verification_code'] !== $fields['verification_code']) {
                return ['error' => 'Invalid verification code.'];
            }
            
            User_model::update_code($user['id']);

            return ['user_id' => $user['id'],  'message' => 'Email verified successfully!'];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public static function verifyProfileById(mixed $authorization, array $data)
    {
        try {
            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'User not logged.'];
            
            $fields = Validator::validate([
                'user_id' => $data['user_id'] ?? ''
            ]);

            $user = User_Model::findProfilePicture($fields['user_id']);


        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function update_profile_image($data)
    {
        
        if (!is_array($data)) {
            return ['error' => 'Invalid data format.'];
        }
    
     
        $user_id = $data['user_id'];
        $profile_img = $data['profile_picture'];
        $bio = $data['bio'];
        
        $update_result = User_model::update_profile_image($user_id, $profile_img,$bio);
    
        if (!$update_result) {
            return ['error' => 'Error updating profile image.'];
        }
    
        return ['success' => 'Profile image updated successfully.'];
    }

    
    public static function register_bio($data)
    {
        $bio = $data['bio'];
        $user_id = $data['user_id'];


        $user = User_model::register_bio($bio,$user_id);
    
        if (!$user) {
            return ['error' => 'Error registering your bio.'];
        }
    
        return ['success' => 'Bio registering successfully.'];
    }
    
    


    

    public static function auth(array $data)
    {
        try {
            $fields = Validator::validate([
                'email' => $data['email'] ?? '',
                'password' => $data['password'] ?? ''
            ]);

            $user = User_model::authentication($fields);

            if(!$user) return ['error' => 'Sorry, we could not authenticate you.'];

            if($user['verification_code'] !== null){
                return ['error' => 'Unverified user, check your email and validate your account.'];
            }

            return JWT::generate($user);
        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }
        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }


    public static function fetch_verify_email(array $data)
    {
        try {
            $user = User_model::fetch_verify_email($data);

            if(!$user) return ['error' => 'Sorry, we couldnot find your account.'];

            return $user;
        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function fetch(mixed $authorization, string $name)
    {
        try {

            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'Please, login to access this resource.'];

            $user = User_model::find($name);

            if(!$user) return ['error' => 'Sorry, we couldnot find your account.'];

            return $user;
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

            $fields = Validator::validate([
                'name' => $data['name'] ?? ''
            ]);

            $user = User_model::update($user_from_JWT['id'], $fields);

            if(!$user) return ['error' => 'Sorry, we could not update your account.'];

            return "User update succesfully.";

        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function delete(mixed $authorization)
    {
        try {
            if(isset($authorization['error'])) return ['error' => $authorization['error']];

            $user_from_JWT = JWT::verify($authorization);

            if (!$user_from_JWT) return ['error' => 'Please login to access this resource.'];

           

            $user = User_model::delete($user_from_JWT['id']);


            if(!$user) return ['error' => 'Sorry, we could not update your account.'];

            return "User deleted succesfully.";

        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function register_follower(mixed $authorization, array $data)
    {
        try {
            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'Please, login to access this resource.'];

            $fields = Validator::validate([
                'user_followed_id' => $data['user_followed_id'] ?? '',
                'followed_at' => $data['followed_at'] ?? ''
            ]);

            $user = User_model::register_follower($user_from_JWT['id'],$fields);

            if(!$user) return ['error' => 'Sorry, we could not follow this user'];

            return "User followed successfully!";
        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function get_info(mixed $authorization)
    {
        try {
            if(isset($authorization['error'])){
                return ['error' => $authorization['error']];
            }

            $user_from_JWT = JWT::verify($authorization);

            if(!$user_from_JWT) return ['error' => 'Please, login to access this resource.'];

            $user = User_model::get_info($user_from_JWT['id']);

            if(!$user) return ['error' => 'Sorry, we could not follow this user'];

            return $user;
        } catch (PDOException $e) {
            if($e->getCode() === 1049) return ['error' => 'Sorry, we could not connect to the database'];
        }

        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
}
