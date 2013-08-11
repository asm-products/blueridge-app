<?php
/**
 * Doorman
 */

namespace BlueRidge\Utilities;

class Doorman
{
    public static function Init()
    {
        $password = self::getPassword();
        $key = password_hash($password, PASSWORD_BCRYPT);
        return ['key'=>$key,'password'=>$password];
    }

    public static function Authorize($password,$key)
    {
        return password_verify($password, $key); 
    }

    public static function getPassword()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&-_";           
        $password = substr(str_shuffle( $chars ), 0, 10 );
        return $password;
    }
}