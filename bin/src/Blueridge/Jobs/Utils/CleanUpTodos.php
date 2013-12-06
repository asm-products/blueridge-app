<?php
/**
 * Clean Up Todos
 * Filter todos from storage */ 
namespace Blueridge\Jobs\Utils;

use Blueridge\Blueridge;
use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Blueridge\Providers\Basecamp;

class CleanUpTodos 
{
    public function perform()
    {

        $blueridge= new Blueridge();
        
        
        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');


        $user= $userQr->findOneById($this->args['userId']);        

        $projects = (!empty($this->args['projects']))?$this->args['projects']:null;
        $collection = $todoQr->fetchByUser($user);

        $cleanedCount = 0;
        foreach ($collection as $todo ) {
            $basecampClient = new Basecamp($blueridge);
            $item = $basecampClient->getTodo($user,$todo->source['url']);

            if(empty($item)){                  
                $blueridge['documentManager']->remove($todo);                 
                $blueridge['documentManager']->flush(); 
                $cleanedCount++;            
            } 

            if($item['completed'] === true){
                $blueridge['documentManager']->remove($todo);                 
                $blueridge['documentManager']->flush(); 
                $cleanedCount++; 
            }            
            
        }
        echo "cleaned {$cleanedCount} todos";        
    }

}