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
        ->eagerCursor(true)    
        ->field('todoId')->in($todoIds)
        ->getQuery()->execute();
    }

    
    public function fetchByUser(\Blueridge\Documents\User $user)
    {        
        return $this->createQueryBuilder()
        ->eagerCursor(true)    
        ->field('rel.project.id')->in($user->profile['projects'])
        ->getQuery()->execute();
    }

}