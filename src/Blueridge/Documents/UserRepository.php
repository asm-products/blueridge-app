<?php
/**
 * User Repository
 */
namespace BlueRidge\Documents;

use Doctrine\ODM\MongoDB\DocumentRepository;

class UserRepository extends DocumentRepository
{
    public function exists($property, $value)
    {


        // return $this->createQueryBuilder()
        //     ->field('status')->equals('admin')
        //     ->getQuery()->execute();
    }
}