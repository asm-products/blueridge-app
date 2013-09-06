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

        return ['key'=>$service['publishable_key'],'card'=>[
        'id'=>$card->id,
        'last4'=>$card->last4,
        'exp_month' => $card->exp_month,
        'type'=>$card->type,
        'exp_year' => $card->exp_year
        ]];
    }

    public static function updatePayment($service,$customerId,$token)
    {
        \Stripe::setApiKey($service['secret_key']);    
        $customer = \Stripe_Customer::retrieve($customerId);
        /**
        * @todo delete any cards before adding a new one
        */

        $card = $customer->cards->create(array("card" => $token));
        
        return ['key'=>$service['publishable_key'],'card'=>[
        'id'=>$card->id,
        'last4'=>$card->last4,
        'type'=>$card->type,
        'exp_month' => $card->exp_month,
        'exp_year' => $card->exp_year
        ]];
    }

    public static function updateSubscription($service,$customerId,$plan)
    {
        \Stripe::setApiKey($service['secret_key']);    
        $customer = \Stripe_Customer::retrieve($customerId);
        $customer->updateSubscription(array("plan" => $plan, "prorate" => true));

        return [
        'id'=>$customer->subscription->plan->id,
        'name'=>$customer->subscription->plan->name,
        ];
    }
}