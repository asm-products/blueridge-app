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
            $code = self::generateCode();    
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

    /**
     * Generate a 16 digit random code
     * @return String code
     */
    public static function generateCode()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#-_&%$*(){}[]=+;:";           
        $password = substr(str_shuffle( $chars ), 0, 16 );
        return $password;
    }
}