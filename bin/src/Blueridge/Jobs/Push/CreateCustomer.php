<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Jobs\Push;

use Blueridge\Application;
use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Stripe;
use Stripe_Customer;


class CreateCustomer
{
	/**
	 * Create a Stripe Customer
	 * @return Void
	 */
	public function perform()
	{
		$blueridge= new Application();
        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');

        $user= $userQr->findOneById($this->args['user']['id']); 
        if(empty($user)){
            return;
        } 
        
        Stripe::setApiKey($this->args['service']['secret_key']);
        $customer = Stripe_Customer::create(['description' => $user->name,'email' =>$user->email,'plan'=>'br-free']); 

        $subscription = [
        'customerId'=>$customer->id,
        'plan'=>['id'=>$customer->subscription->plan->id,'name'=>$customer->subscription->plan->name],
        'card'=>['id'=>'','type'=>'','last4'=>'','exp_month'=>'','exp_year'=>''],
        'status'=>$customer->subscription->status
        ];

        $userQr->setSubscription($user,$subscription); 
        

    }
}