<?php
/**
 * Doorman
 */

namespace Blueridge\Utilities;

class Teller
{
    /**
     * Add Customer
     * Adds a new customer to Stripe
     */
    public static function addCustomer($service,$user)
    {
        \Stripe::setApiKey($service['stripe']['secret_key']);
        $customer = \Stripe_Customer::create(['description' => $user['name'],'email' =>$user['email'],'plan'=>'br-free']);

        return [
        'customerId'=>$customer->id,
        'plan'=>['id'=>$customer->subscription->plan->id,'name'=>$customer->subscription->plan->name],
        'card'=>['id'=>'','type'=>'','last4'=>'','exp_month'=>'','exp_year'=>''],
        'status'=>$customer->subscription->status
        ];

    }

    /**
     * Update Customer Payment Method
     */
    public static function updatePayment($service,$customerId,$token)
    {
        \Stripe::setApiKey($service['stripe']['secret_key']);
        $customer = \Stripe_Customer::retrieve($customerId);
        /**
        * @todo delete any cards before adding a new one
        */

        $card = $customer->cards->create(array("card" => $token));

        return [
        'id'=>$card->id,
        'type'=>$card->type,
        'last4'=>$card->last4,
        'exp_month' => $card->exp_month,
        'exp_year' => $card->exp_year
        ];
    }

    public static function updateSubscription($service,$customerId,$plan)
    {
        \Stripe::setApiKey($service['stripe']['secret_key']);
        $customer = \Stripe_Customer::retrieve($customerId);
        $customer->updateSubscription(array("plan" => $plan, "prorate" => true));

        return [
        'id'=>$customer->subscription->plan->id,
        'name'=>$customer->subscription->plan->name,
        ];
    }
}