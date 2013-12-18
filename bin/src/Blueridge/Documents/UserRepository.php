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
     * Add stripe subscription Plan
     * @param  Object $user 
     * @param  Array $subscription 
     * @return Object      
     */
    public function addSubscription(\Blueridge\Documents\User $user,$subscription)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('subscription')->set($subscription)
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

    /**
     * Update User projects
     */
    public function updateProjects(\Blueridge\Documents\User $user, Array $projects)
    {
        return $this->createQueryBuilder()
        ->findAndUpdate()
        ->returnNew()
        ->field('projects')->set($projects)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Update User Profile segment
     */
    public function updateProfile(\Blueridge\Documents\User $user, $segment, Array $properties)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('profile.'.$segment)->set($properties)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Update User projects
     */
    public function updateStatus(\Blueridge\Documents\User $user, $status)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('status')->set($status)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Update User url
     */
    public function updateUrl(\Blueridge\Documents\User $user, $url)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('url')->set($url)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

}