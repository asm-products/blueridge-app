<?php
/**
 * Doorman
 */

namespace BlueRidge\Utilities;

class Teller
{
    /**
     * Add Customer
     * Adds a new customer to Stripe
     */
    public static function addCustomer($service,$user)
    {
        \Stripe::setApiKey($service['secret_key']);
        $customer = \Stripe_Customer::create(['description' => $user['name'],'email' =>$user['email'],'plan'=>'br-free']);

       return [
        'customerId'=>$customer->id,
        'plan'=>['id'=>$customer->subscription->plan->id,'name'=>$customer->subscription->plan->name],
        'cardId'=>null,
        'status'=>$customer->subscription->status
        ];

    }

    public static function getPayment($service,$subscription)
    {
        if(empty($subscription['cardId']))
        {
            return ['key'=>$service['publishable_key'],'card'=>null];
        }

        \Stripe::setApiKey($service['secret_key']);        
        $customer = \Stripe_Customer::retrieve($subscription['customerId']);
        $card = $customer->cards->retrieve($subscription['cardId']);

        return ['key'=>$service['publishable_key'],'card'=>$card];
    }

    public static function updatePayment($service,$token)
    {
        return;
    }

}