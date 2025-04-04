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

    public static function for_you($limit,$offset)
    {

        
        $pdo = self::get_connection();

        $stmt = $pdo->prepare('
            SELECT p.*, u.* FROM post as p, user as u WHERE (p.user_id = u.user_id)
            ORDER BY post_id DESC
            LIMIT :limit OFFSET :offset
        ');
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
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

    public static function create_comment(int|string $id, array $data){
        $pdo = self::get_connection();

        $stmt1 = $pdo->prepare('INSERT INTO comment (post_id,user_id,comment,commented_at)
        VALUES (?,?,?,?)');

        $stmt1->execute([
            $data['post_id'],
            $id,
            $data['comment'],
            $data['commented_at']
        ]);
        //se conseguiu inserir o comentario manda a notificação
        if ($stmt1->rowCount() > 0) {
            $stmt2 = $pdo->prepare('INSERT INTO `notification` (type_notification, user_id, notification_message, notification_at, notification_read)
            VALUES (?, ?, ?, ?, ?)');
               $stmt2->execute([
                "new_comment",
                $data['user_post_commented_id'],
                "You have a newe comment on your post",
                $data['commented_at'],
                0
                ]);
                return "Notification generated";
            }

        return $pdo->lastInsertId() > 0 ? true : false;
    }

    public static function register_like(int|string $id, array $data){
        $pdo = self::get_connection();

        $stmt1 = $pdo->prepare('SELECT post_id,user_id FROM `like` WHERE post_id = ? and user_id = ?');
        $stmt1->execute([
            $data['post_id'],
            $id]);
        $tanned = $stmt1->fetchColumn();

        if($tanned){
            $stmt_delete = $pdo->prepare('DELETE FROM `like` WHERE post_id = ? and user_id = ?');
            $stmt_delete->execute([
                $data['post_id'],
                $id]);
            return 'deleted';
        }
        else{
            $stmt2 = $pdo->prepare('INSERT INTO `like` (post_id,user_id,liked_at)
            VALUES (?,?,?)');
    
            $stmt2->execute([
                $data['post_id'],
                $id,
                $data['liked_at']
            ]);
            $resp = $pdo->lastInsertId();
        

        if ($stmt2->rowCount() > 0) {
            $stmt3 = $pdo->prepare('INSERT INTO `notification` (type_notification, user_id, notification_message, notification_at, notification_read)
            VALUES (?, ?, ?, ?, ?)');
               $stmt3->execute([
                "new_like",
                $data['user_post_liked_id'],
                "You have a newe like on your post",
                $data['liked_at'],
                0
                ]);
                return "Notification generated";
            }
        }
    }
}