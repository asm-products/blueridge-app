<?php
/**
 * Clean Up Todos
 * Filter todos from storage */
namespace Blueridge\Jobs;

use Blueridge\Application;
use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Blueridge\Providers\Basecamp\BasecampClient;
use Blueridge\Providers\Basecamp\Helper as ServiceHelper;

class FilterBasecampTodosByUser
{

    public function perform()
    {
        $blueridge= new Application();
        $userQr=$blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $todoQr=$blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');


        $user= $userQr->find($this->args['userid']);
        if(empty($user)){
            return;
        }

        $projects = $user->profile['projects'];
        $collection = $todoQr->fetchByUser($user);

        $service = BasecampClient::factory([
            'token'    => $user->providers['basecamp']['token']['access_token'],
            'user_agent' => $blueridge['configs']['providers']['basecamp']['user_agent']
            ]);

        $serviceHelper = new ServiceHelper($service);

        foreach ($collection as $todo ) {
            $item = $service->getTodo([
                'accountId'=>$todo->rel['project']['account']['id'],
                'projectId'=>$todo->rel['project']['id'],
                'todoId'=>$todo->source['id'],
                ]);

            if(empty($item)){
                $blueridge['documentManager']->remove($todo);
                $blueridge['documentManager']->flush();
            }

            if($item['completed'] === true){
                $blueridge['documentManager']->remove($todo);
                $blueridge['documentManager']->flush();
            }

        }
    }

}