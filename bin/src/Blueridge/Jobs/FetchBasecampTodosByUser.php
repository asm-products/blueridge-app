<?php
/**
 * Pull Todos
 * Fetch a users todos from providers
 */
namespace Blueridge\Jobs;

use Blueridge\Application;
use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Blueridge\Providers\Basecamp\BasecampClient;
use Blueridge\Providers\Basecamp\Helper as ServiceHelper;

class FetchBasecampTodosByUser
{
    public function perform()
    {

        $blueridge= new Application();

        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');


        $user= $userQr->findOneById($this->args['userid']);
        if(empty($user)){
            return;
        }

        foreach ($user->profile['projects'] as $selectedProject) {
            $projectTodos = $todoQr->fetchByProject($user,$selectedProject)->toArray();

            if(empty($todos)) {
                $providerConfigs = $blueridge['configs']['providers']['basecamp'];
                $service = BasecampClient::factory([
                    'token'    => $user->providers['basecamp']['token']['access_token'],
                    'user_agent' => $providerConfigs['user_agent'],
                    ]);
                $serviceHelper = new ServiceHelper($service);
                $todoItems = $serviceHelper->fetchTodos($user,[$selectedProject]);

                if(!empty($todoItems)) {
                    $todoIterator = new \ArrayIterator($todoItems);
                    foreach($todoIterator as $todoItem){

                        $todoItem=$serviceHelper->toStoreage($todoItem);
                        $todo = $todoQr->findOneBy(['todoId'=>$todoItem['todoId']]);

                        if (empty($todo)) {
                            $todo = new Todo();
                            $todo->setProperties($todoItem);
                            $blueridge['documentManager']->persist($todo);
                            $blueridge['documentManager']->flush();
                        }

                    }
                }

            }
        }

    }

}