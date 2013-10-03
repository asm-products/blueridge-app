<?php
/**
 * Doorman
 */

namespace Blueridge\Utilities;

class Doorman
{
    public static function getAccess($password = null)
    {
        if(empty($password)){
            $password = self::generatePassword();    
        }
        
        $key = password_hash($password, PASSWORD_BCRYPT);
        return ['key'=>$key,'password'=>$password];
    }

    public static function authorize($password,$key)
    {
        return password_verify($password, $key); 
    }

    public static function generatePassword()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&-_";           
        $password = substr(str_shuffle( $chars ), 0, 10 );
        return $password;
    }
}