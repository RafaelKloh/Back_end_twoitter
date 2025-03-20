<?php

namespace App\Models;
use PDO;

Class Database
{
    public static function get_connection()
    {
        $pdo = new PDO("mysql:dbname=twoitter;host=localhost", "root", "");
        return $pdo;
    }
}