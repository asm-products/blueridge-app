<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Providers\Basecamp;

use Blueridge\Providers\Basecamp\BasecampClient as Service;
use Blueridge\Documents\User;

class Helper 
{
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function fetchProjects(User $user)
    {        
        var_dump($user->providers['basecamp']);
        exit();
        // $endpoint="projects.json";
        // $projects=[];
        // $index=0;

        // $this->setAuth($user->providers['basecamp']['token']);
        // $accountIterator = new \ArrayIterator($user->providers['basecamp']['accounts']);        
        // foreach ($accountIterator as $account) {            
        //     $list = $this->client->get("{$account['href']}/{$endpoint}")->send()->json();

        //     if(!empty($list)){
        //         array_walk($list, function(&$project, $key, $account) {
        //             $project['account'] = $account;                    
        //         },$account);

        //         $projects = array_merge($projects,$list);     
        //     }            
        // }
        // return $projects;
    }

    /**
     * Return all the users projects 
     * @param  User   $user
     * @return Array
     */
    public function fetchTodos(User $user, $projectIds = null)
    {        
        $projectIds = (empty($projectIds))?$user->profile['projects']:$projectIds;
        $todos = [];
        $todolists = $this->fetchTodolists($user, $projectIds);
        if (empty($todolists)) {
            return;
        }

        $todolistIterator = new \ArrayIterator($todolists); 

        foreach ($todolistIterator as $todolist) {

            $basecampTodolist = $this->service->getTodolist([
                'accountId'=>$todolist['rel']['project']['account']['id'],
                'projectId'=>$todolist['rel']['project']['id'],
                'todolistId'=>$todolist['id']
                ]);
            $list = $basecampTodolist['todos']['remaining'];
            $rel=['project'=>$todolist['rel']['project'],'list_name'=>$basecampTodolist['name']];

            array_walk($list, function(&$a, $key, $rel) {             
                $a['rel'] = $rel;                
                $a['rel']['href'] = $this->getSiteUrl($a['url']);                
            },$rel);

            $todos = array_merge($todos,$list);  
        }
        return $todos;
    }

    /**
     * Fetch all todolists that can be accessed by a user
     * @param  User   $user               
     * @param  Array $selectedProjectIds 
     * @return Array
     */
    public function fetchTodolists(User $user, Array $selectedProjectIds)
    {

        $todolists = [];
        $projectIterator = new \ArrayIterator($user->projects);

        foreach ($projectIterator as $project) {
            if(in_array($project['id'], $selectedProjectIds)){                

                $todolist = $this->service->getTodolistsByProject([
                    'accountId'=>$project['account']['id'],
                    'projectId'=>$project['id']
                    ]);

                if(!empty($todolist)) {
                    array_walk($todolist, function(&$a, $key, $project) {
                        $a['rel']['project'] = $project;                    
                    },$project);
                    $todolists = array_merge($todolists,$todolist);
                }                
            }   
        }

        return $todolists;
    }

    /**
     * Return a href from a  filtered api endpoint
     * @param  Url $url 
     * @return Url
     */
    protected function getSiteUrl($url){
        $points = ['/api/v1','.json'];
        $siteUrl = str_replace($points,'',$url);
        return $siteUrl;
    }
}