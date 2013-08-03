<?php
/**
*
*/
function cashier_subscribe($app,$user,$params)
{
    try{
        Stripe::setApiKey($app->cashier->secret_key);

        $customer = Stripe_Customer::create(array(
          "card" => $params->token,
          "plan" => $params->plan,
          "email" =>$user->email)
        );
        return $customer;        
    }catch(Error $e){
        error_log($e->getMessage());
    }
    
}