<?php
/**
 * User Repository
 */
namespace Blueridge\Documents;

use Doctrine\ODM\MongoDB\DocumentRepository;

class UserRepository extends DocumentRepository
{

    public function updatePayment($user,$payment)
    {
        return $this->createQueryBuilder()
        ->update()
        ->field('subscription.card')->set($payment)
        ->field('id')->equals($user->id)
        ->getQuery()->execute();
    }

    public function updateSubscription($user,$plan)
    {
       return $this->createQueryBuilder()
       ->update()
       ->field('subscription.plan')->set($plan)
       ->field('id')->equals($user->id)
       ->getQuery()->execute();
   }


}