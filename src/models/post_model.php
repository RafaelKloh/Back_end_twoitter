<?php

namespace App\Models;

use App\Models\Database;
use PDO;
Class Post_model extends Database
{
    public static function create(int|string $id, array $data){
        $pdo = self::get_connection();

        $stmt = $pdo->prepare("
            INSERT INTO post (user_id, text_post, image_post, posted_at)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $id,
            $data['text_post'],
            $data['image_post'],
            $data['posted_at']
        ]);


        $stmt = $pdo->prepare("
        INSERT INTO tag (tag_description) 
        VALUES (?)
        ");

        $stmt->execute([$data['tag_description']]);

        return $pdo->lastInsertId() > 0 ? true : false;
    }

}