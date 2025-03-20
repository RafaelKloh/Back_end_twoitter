<?php
namespace App\Utils;

Class Validator
{
    public static function validate(array $fields)
    {
        foreach ($fields as $field => $value) {
            if(empty(trim($value))) {
                throw new \Exception("The field ($field) is required.");
            }
        }
        return $fields;
    }
}