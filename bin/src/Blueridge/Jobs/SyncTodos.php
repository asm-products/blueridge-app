<?php
/**
 * Clean Up Todos
 * Filter todos from storage */
namespace Blueridge\Jobs;

use Blueridge\Application;
use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Blueridge\Providers\Basecamp;

class SyncTodos
{
    public function perform()
    {

        $blueridge= new Application();

        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');


        $user= $userQr->findOneById($this->args['user_id']);
        if(empty($user)){
            return;
        }

        // $projects = (!empty($this->args['projects']))?$this->args['projects']:null;
        $projects = $user->profile['projects'];
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