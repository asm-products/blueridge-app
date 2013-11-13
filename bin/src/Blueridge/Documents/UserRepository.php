<?php
/**
 * User Repository
 */
namespace Blueridge\Documents;

use Doctrine\ODM\MongoDB\DocumentRepository;

class UserRepository extends DocumentRepository
{
    /**
     * Update User Payment Method
     * @param  Object $user    
     * @param  Array $payment 
     * @return Object
     */
    public function updatePayment($user,$payment)
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
    public function updateSubscription($user,$plan)
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

    // public function fetchProjects()
    // {

    // }


}