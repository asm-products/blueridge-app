<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Documents;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * User Repository
 */
class UserRepository extends DocumentRepository
{
    /**
     * Update User Payment Method
     * @param  Object $user    
     * @param  Array $payment 
     * @return Object
     */
    public function updatePayment(\Blueridge\Documents\User  $user,$payment)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('subscription.card')->set($payment)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Update Subscription Plan
     * @param  Object $user 
     * @param  Array $plan 
     * @return Object      
     */
    public function updateSubscription(\Blueridge\Documents\User $user,$plan)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('subscription.plan')->set($plan)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Fetch All Users
     * @return Object Array
     */
    public function fetchAll()
    {        
        return $this->createQueryBuilder()
        ->eagerCursor(true)       
        ->getQuery()->execute();
    }

    /**
     * Add Provider
     * @param  Object $user    
     * @param  String $providerName 
     * @param  Array $ProviderDetails 
     * @return Object
     */
    public function addProvider(\Blueridge\Documents\User $user, $providerName, Array $providerDetails)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('providers.'.$providerName)->set($providerDetails)        
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

}