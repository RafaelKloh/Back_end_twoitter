<?php

namespace App\Models;

use App\Models\Database;
use PDO;

class User_model extends Database
{
    public static function save(array $data, string|int $verification_code, string $bio, string $profile_img)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare("
            INSERT INTO user (name,email,password,sex,profile_picture_url,bio,user_birth_date,user_creation_date,verification_code)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'],
            $data['sex'],
            $profile_img,
            $bio,
            $data['user_birth_date'],
            $data['user_creation_date'],
            $verification_code
        ]);

        return $pdo->lastInsertId() > 0 ? true : false;
    }



    public static function update_profile_image(int|string $user_id, $img, string $bio)
    {
        $pdo = self::get_connection();


        $stmt = $pdo->prepare("UPDATE user SET profile_picture_url = ?, bio = ? WHERE user_id = ?");

        $stmt->execute([
            $img,
            $bio,
            $user_id
        ]);

        if ($stmt->rowCount() > 0) {
            return ['success' => true];
        } else {
            return ['error' => 'No changes made or user not found.'];
        }
    }

    public static function register_bio(string $bio, int|string $user_id)
    {
        $pdo = self::get_connection();


        $stmt = $pdo->prepare("UPDATE user SET bio = ? WHERE user_id = ?");

        $stmt->execute([
            $bio,
            $user_id
        ]);

        if ($stmt->rowCount() > 0) {
            return ['success' => true];
        } else {
            return ['error' => 'No changes made or user not found.'];
        }
    }

    public static function findByEmail($email)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            SELECT * FROM user WHERE email = ?
        ');

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'id' => $user['user_id'],
            'verification_code' => $user['verification_code']
        ];
    }
    public static function findProfilePicture($userId)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            SELECT profile_picture_url FROM user WHERE user_id = ?
        ');

        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update_code($id)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
           UPDATE user SET verification_code = NULL WHERE user_id = ?;
        ');

        $stmt->execute([$id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return [$user];
    }

    public static function authentication(array $data)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare("
            SELECT * FROM user WHERE email = ?
        ");
        $stmt->execute([$data['email']]);

        if ($stmt->rowCount() < 1) return false;

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($data['password'], $user['password'])) return false;

        return [
            'id' => $user['user_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'verification_code' => $user['verification_code']
        ];
    }

    public static function fetch_verify_email(array $data)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare("
            SELECT verification_code FROM user WHERE email = ?
        ");
        $stmt->execute([$data['email']]);

        if ($stmt->rowCount() < 1) return false;

        $user = $stmt->fetch(PDO::FETCH_ASSOC);



        return [
            'verification_code' => $user['verification_code']
        ];
    }

    public static function find(int|string $name, $limit, $offset)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            SELECT * FROM user WHERE name like :name
            ORDER BY name DESC

            LIMIT :limit OFFSET :offset;
        ');
        $search = "%$name%";

        $stmt->bindValue(':name', $search, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public static function update(int|string $id, array $data)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            UPDATE user SET name = ? WHERE user_id = ?
        ');

        $stmt->execute([$data['name'], $id]);

        return $stmt->rowCount() > 0 ? true : false;
    }

    public static function delete(int|string $id)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            DELETE FROM user WHERE user_id = ?
        ');

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0 ? true : false;
    }

    public static function register_follower(int|string $id, array $data)
    {
        $pdo = self::get_connection();

        // Verifica se o usuário já segue o outro
        $stmt1 = $pdo->prepare('SELECT user_follower_id FROM follower WHERE user_follower_id = ? AND user_followed_id = ?');
        $stmt1->execute([
            $id,
            $data['user_followed_id']
        ]);
        $following = $stmt1->fetchColumn();

        if ($following) {
            return false;
        }

        // Se não seguia, faz o insert
        $stmt2 = $pdo->prepare('INSERT INTO follower (user_follower_id, user_followed_id, followed_at) VALUES (?, ?, ?)');
        $stmt2->execute([
            $id,
            $data['user_followed_id'],
            $data['followed_at']
        ]);

        // Verifica se conseguiu inserir o usuario e se conseguiu manda a notificação
        if ($stmt2->rowCount() > 0) {
            $stmt3 = $pdo->prepare('INSERT INTO `notification` (type_notification, user_id, notification_message, notification_at, notification_read)
            VALUES (?, ?, ?, ?, ?)');
            $stmt3->execute([
                "new_follower",
                $data['user_followed_id'],
                "You have a new follower!",
                $data['followed_at'],
                0
            ]);
            return "Notification generated";
        }

        return false;
    }

    public static function get_info(int|string $id)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            SELECT * FROM user WHERE user_id = ?
        ');

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
