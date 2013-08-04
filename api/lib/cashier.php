<?php
/**
*
*/
function cashier_subscribe($app,$user,$params)
{
    switch($params->plan){
        case 'solo':
        $plan = 'BR-Solo';
        break;
        case 'pro':
        $plan='BR-Pro';
        break;
    }

    try{
        Stripe::setApiKey($app->cashier->secret_key);

        $customer = Stripe_Customer::create(array(
          "card" => $params->payment->id,
          "plan" => $plan,
          "email" =>$user->email)
        );
        return $customer;        
    }catch(Error $e){
        error_log($e->getMessage());
    }
    
}