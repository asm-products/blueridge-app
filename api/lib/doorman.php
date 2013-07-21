<?php
/**
 * Doorman
 * Protecting the masses
 */

function doorman_welcome()
{
    $pass = doorman_pass();
    $key = password_hash($pass, PASSWORD_BCRYPT);
    return ['key'=>$key,'pass'=>$pass];
}
function doorman_authorize($password,$key)
{
    return password_verify($password, $key); 
}

function doorman_pass()
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&-_";           
    $pass = substr(str_shuffle( $chars ), 0, 10 );
    return $pass;
}
