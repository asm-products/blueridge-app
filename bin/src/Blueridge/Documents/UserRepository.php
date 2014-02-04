<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Documents;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Blueridge\Documents\User;

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
    public function updatePayment(User  $user,$payment)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('subscription.card')->set($payment)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Set stripe subscription plan
     * @param  Object $user
     * @param  Array $subscription
     * @return Object
     */
    public function setSubscription(User $user,$subscription)
    {
        return $this->createQueryBuilder()
        ->findAndUpdate()
        ->returnNew()
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
    public function updateSubscriptionPlan(User $user,$plan)
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
     * Set Provider
     * @param  Object $user
     * @param  String $providerName
     * @param  Array $ProviderDetails
     * @return Object
     */
    public function setProvider(User $user, $providerName, Array $providerDetails)
    {
        return $this->createQueryBuilder()
        ->findAndUpdate()
        ->returnNew()
        ->field('providers.'.$providerName)->set($providerDetails)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Set Identifier Key
     * @param  Object $user
     * @param  String $identifierKey
     * @return Object
     */
    public function setIdentifierKey(User $user, $identifierKey)
    {
        return $this->createQueryBuilder()
        ->findAndUpdate()
        ->returnNew()
        ->field('identifierKey')->set($identifierKey)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Update Projects
     * @param  User   $user
     * @param  Array  $projects
     * @return object
     */
    public function updateProjects(User $user, Array $projects)
    {
        return $this->createQueryBuilder()
        ->findAndUpdate()
        ->returnNew()
        ->field('projects')->set($projects)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Update Profile
     * @param  User   $user
     * @param  string $segment
     * @param  Array  $properties
     * @return object
     */
    public function updateProfile(User $user, $segment, Array $properties)
    {
        return $this->createQueryBuilder()
        ->findAndUpdate()
        ->returnNew()
        ->field('profile.'.$segment)->set($properties)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    /**
     * Set Status
     * @param User   $user
     * @param string $status
     * @return object
     */
    public function setStatus(User $user, $status)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('status')->set($status)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

}