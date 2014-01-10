<?php
/**
 * Mapster
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 * @since v0.1.0
 */

namespace Blueridge\Utilities;

/**
 * Doorman
 * 
 * Doorman helps you generate and validate keys and access codes
 * @package Blueridge
 */
class Doorman
{
    /**
     * get a generated code
     * @param  String $code Can be null
     * @return Array
     */
    public static function getCode($code = null)
    {
        if(empty($code)){
            $code = self::getToken();  
        }

        $key = password_hash($code, PASSWORD_BCRYPT);
        return ['key'=>$key,'code'=>$code];
    }
    
    /**
     * Validate code against key
     * 
     * @param String $code
     * @param String $key
     */
    public static function validate($code,$key)
    {
        return password_verify($code, $key);
    }

    public static function getToken($length = 8)
    {
        return bin2hex(openssl_random_pseudo_bytes($length));

    } 
}