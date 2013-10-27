<?php
/**
 * Todo Repository
 */
namespace Blueridge\Documents;

use Doctrine\ODM\MongoDB\DocumentRepository;

class TodoRepository extends DocumentRepository
{

    public function fetchByTodoId(Array $todoIds)
    {

        return $this->createQueryBuilder()
        ->hydrate(false)    
        ->field('todoId')->in($todoIds)
        ->getQuery()->execute();
    }

    
    public function fetchByUser(\Blueridge\Documents\User $user)
    {        
        return $this->createQueryBuilder()
        ->hydrate(false)    
        ->field('rel.project.id')->in($user->profile['projects'])
        ->getQuery()->execute();
    }

}