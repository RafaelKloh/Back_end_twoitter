<?php

namespace App\Models;

use App\Models\Database;
use PDO;
Class User_model extends Database
{
    public static function save(array $data){
        $pdo = self::get_connection();

        $stmt = $pdo->prepare("
            INSERT INTO user (name,email,password,profile_picture_url,bio,user_birth_date,user_creation_date)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'],
            $data['profile_picture_url'],
            $data['bio'],
            $data['user_birth_date'],
            $data['user_creation_date']
        ]);

        return $pdo->lastInsertId() > 0 ? true : false;
    }

    public static function authentication(array $data)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare("
            SELECT * FROM user WHERE email = ?
        ");
        $stmt->execute([$data['email']]);

        if($stmt->rowCount() < 1) return false;

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!password_verify($data['password'], $user['password'])) return false;

        return [
            'id' => $user['user_id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
    }

    public static function find(int|string $name)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            SELECT name, email, profile_picture_url, bio, user_birth_date, user_creation_date FROM user WHERE name = ?
        ');

        $stmt->execute([$name]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update(int|string $id, array $data)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            UPDATE user SET name = ? WHERE user_id = ?
        ');

        $stmt->execute([$data['name'],$id]);

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
}