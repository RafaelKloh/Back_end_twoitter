<?php

namespace App\Models;

use App\Models\Database;
use PDO;
Class Post_model extends Database
{
    public static function create(int|string $id, array $data){
        $pdo = self::get_connection();

        $stmt1 = $pdo->prepare("
            INSERT INTO post (user_id, text_post, image_post, posted_at)
            VALUES (?, ?, ?, ?)
        ");

        $stmt1->execute([
            $id,
            $data['text_post'],
            $data['image_post'],
            $data['posted_at']
        ]);

        $post_id = $pdo->lastInsertId();

        if(!empty($data['tags']))
        {
            foreach ($data['tags'] as $tag) {
                $stmt2 = $pdo->prepare("SELECT tag_id FROM tag WHERE tag_description = ?");
                $stmt2->execute([$tag]);
                $tag_id = $stmt2->fetchColumn();

                if (!$tag_id) {
                    $stmt3 = $pdo->prepare("INSERT INTO tag (tag_description) VALUES (?)");
                    $stmt3->execute([$tag]);
                    $tag_id = $pdo->lastInsertId(); // ID da nova tag
                }

                $stmt4 = $pdo->prepare("
                    INSERT INTO post_tag (post_id, tag_id) 
                    VALUES (?, ?)
                ");
                $stmt4->execute([$post_id, $tag_id]);
            }
        }

        return $pdo->lastInsertId() > 0 ? true : false;
    }

    public static function find(string $search){
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('SELECT text_post , image_post
        FROM post 
        WHERE text_post LIKE ?');

        $stmt->execute(["%$search%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find_tag(string $search){
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('SELECT p.text_post, p.image_post, t.tag_description FROM post p, tag t, post_tag pt
            WHERE t.tag_description = ? and pt.tag_id = t.tag_id and p.post_id = pt.post_id;');

        $stmt->execute([$search]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function update(array $data)
    {
        $pdo = self::get_connection();


        //muda se tiver so imagem
        if($data['new_text'] === "" && $data['new_image'] !== ""){
            $stmt = $pdo->prepare('
            UPDATE post SET image_post = ? WHERE post_id = ?
        ');
        $stmt->execute([
            $data['new_image'],
            $data['post_id']]);

        }


        //muda se tiver so texto
        else if($data['new_text'] !== "" && $data['new_image'] === ""){
            $stmt = $pdo->prepare('
            UPDATE post SET text_post = ? WHERE post_id = ?
        ');
        $stmt->execute([
            $data['new_text'],
            $data['post_id']]);
        }

        //muda se tiver os dois
        else{
            $stmt = $pdo->prepare('
            UPDATE post SET text_post = ?, image_post = ? WHERE post_id = ?
        ');
        $stmt->execute([
            $data['new_text'],
            $data['new_image'],
            $data['post_id']]);
            var_dump($data);

        }

        return $stmt->rowCount() > 0 ? true : false;
    }

    public static function delete(array $data)
    {
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            DELETE FROM post WHERE post_id = ?
        ');

        $stmt->execute([$data['post_id']]);

        return $stmt->rowCount() > 0 ? true : false;

    }
}