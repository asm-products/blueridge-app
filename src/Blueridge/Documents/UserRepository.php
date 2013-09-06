<?php
/**
 * User Repository
 */
namespace BlueRidge\Documents;

use Doctrine\ODM\MongoDB\DocumentRepository;

class UserRepository extends DocumentRepository
{

    public function updatePayment($payment)
    {
       return $this->createQueryBuilder()
            ->update()
            ->field('subscription.cardId')->set($payment['card']['id'])
            ->getQuery()->execute();
    }

    public function updateSubscription($plan)
    {
       return $this->createQueryBuilder()
            ->update()
            ->field('subscription.plan')->set($plan)
            ->getQuery()->execute();
    }


}