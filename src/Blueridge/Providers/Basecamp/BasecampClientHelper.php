<?php
/**
 * Basecamp Client Helper
 */

namespace Blueridge\Providers\Basecamp;

use \Blueridge\Documents\User;

class BasecampClientHelper
{
    /**
     * Get Configuration
     */
    public static function getConfig($app,User $user = null)
    {
        $config = $app->config('providers')['basecamp'];

        if(!empty($user))
        {
            $config = array_merge($config,$user->providers['basecamp']['token']);            
        }        
        return $config;
    }

    /**
     * Get Todos
     */
    public static function getTodos($app, User $user)
    {
        $todos = [];
        $clientConfig = self::getConfig($app,$user);        
        $todolists = self::getTodolists($app, $user);

        if (empty($todolists))
        {
            return;
        }
        
        $client = BasecampClient::factory($clientConfig);
        $todolistIterator = new \ArrayIterator($todolists); 

        foreach ($todolistIterator as $todolist) {

            $cacheId = "todos-{$todolist['id']}";
            if ($app->cacheDriver->contains($cacheId)) {
                // fetch list
                $list = $app->cacheDriver->fetch($cacheId);

            } else {
                $request = $client->get($todolist['url']);
                $response = $request->send();
                $bc_todolist = $response->json();
                $list = $bc_todolist['todos']['remaining'];

                $todolist['parent']['list_name'] = $bc_todolist['name'];

                array_walk($list, function(&$a, $key, $parent) {             
                    $a['parent'] = $parent;                    
                    $a['href'] = self::getSiteUrl($a['url']);               
                },$todolist['parent']);
                $app->cacheDriver->save($cacheId, $list);
            }

            $todos = array_merge($todos,$list);            
        }
        return $organized = self::organizeTodos($todos);
    }

    /**
     * Organise Todos
     */
    public static function organizeTodos($todoItems)
    {
        if(empty($todoItems))
        {
            return;
        }

        $todos = [];
        $todosIterator = new \ArrayIterator($todoItems);

        foreach($todosIterator as $key => $todo){
            $initPos = 0;
            $todo['overdue_by'] =null; 

            if(!empty($todo['due_on'])){
                $initPos = 1;
                $dueOn= new \DateTime($todo['due_on']);
                $todo['due_on']=$dueOn->format('m/d/Y');
                $todo['overdue_by'] = (int) self::getOverdueBy($dueOn);
            }
            if(empty($todo['assignee']))
            {
                $todo['assignee'] = ['id'=>null,'type'=>'Person','name'=>'Unassigned'];
            }
            // set initial order
            $due_on[$key] = $todo['due_on'];
            $position[$key] = $initPos;
            $overdue_by[$key] = $todo['overdue_by'];

            $todos[]=$todo;
        }
        array_multisort($overdue_by,SORT_DESC,$position,SORT_DESC,$due_on,SORT_ASC,$todos);
        return $todos;  

    }

    /**
     * Get Todo Lists
     */
    public static function getTodolists($app, User $user)
    {
        $todolists = [];
        $clientConfig = self::getConfig($app,$user);
        $client = BasecampClient::factory($clientConfig);

        $projectIterator = new \ArrayIterator($user->projects);

        foreach ($projectIterator as $project) {
            if($project['selected'])
            {                
                $endpoint = "{$project['account']['href']}/projects/{$project['id']}/todolists.json";
                $request = $client->get($endpoint);
                $response = $request->send();
                $list = $response->json();

                array_walk($list, function(&$a, $key, $project) {
                    $a['parent'] = ['account_name'=>$project['account']['name'],'project_name'=> $project['name']];                    
                },$project);

                $todolists = array_merge($todolists,$list);
            }

        }
        return $todolists;

    }

    /**
     * Get Site Url
     */
    public static function getSiteUrl($url){
        $points = ['/api/v1','.json'];
        $siteUrl = str_replace($points,'',$url);
        return $siteUrl;
    }


    /**
     * Get Over Due By
     */
    public static function getOverdueBy($dueDate)
    {

        $now = new \DateTime('now');

        if($dueDate > $now){
            return 0;
        }

        $interval = $dueDate->diff($now);
        return $interval->format('%a');

    }
}