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
     * Get Token
     */
    public static function getToken ($config,$code)
    {
        $client = BasecampClient::factory($config);
        $params = [
        'type'=>'web_server',
        'client_id'=>$config['client_id'],
        'redirect_uri'=>$config['redirect_uri'],
        'client_secret'=>$config['client_secret'],
        'code'=>$code
        ];


        $request = $client->post($config['token_url'],[],$params);
        $response = $request->send();
        $token = $response->json();

        return $token;
    }

    /**
     * Get Authorization
     */
    public static function getAuthorization($config)
    {
        $endpoint="https://launchpad.37signals.com/authorization.json";     
        $client = BasecampClient::factory($config);

        $request = $client->get($endpoint);
        $response = $request->send();
        $auth = $response->json();

        $authorization = [];

        $authorization['identity'] = $auth['identity'];
        $authorization['expires_at'] = $auth['expires_at'];

        $authIterator = new \ArrayIterator($auth['accounts']);
        foreach ($authIterator as $account) {
            if($account['product'] =='bcx'){
                $authorization['accounts'][] = $account;
            }
        }       
        return $authorization;
    }

    /**
     * Get Me
     */
    public static function getMe($config, $authorization)
    {
        $client = BasecampClient::factory($config);
        $endpoint = "people/me.json";
        
        $url="{$authorization['accounts'][0]['href']}/{$endpoint}";

        $request = $client->get($url);
        $response = $request->send();
        $whoami = $response->json();

        $avatar = parse_url($whoami['avatar_url']);
        return [
        'name'=>$whoami['name'],
        'firstName'=>$authorization['identity']['first_name'],
        'lastName'=>$authorization['identity']['last_name'],
        'email'=>$whoami['email_address'],
        'avatar'=>"//{$avatar['host']}/{$avatar['path']}?{$avatar['query']}"
        ];        
    }

    /**
     * Get Projects
     */
    public static function getProjects($config,$authorization)
    {
        $client = BasecampClient::factory($config);
        $endpoint="projects.json";
        $projects=[];
        $index=0;

        $accountIterator = new \RecursiveArrayIterator($authorization['accounts']);
        foreach (new \RecursiveArrayIterator($accountIterator) as $key => $account) {
            $request = $client->get("{$account['href']}/{$endpoint}");
            $response = $request->send();
            $data= $response->json();

            if(!empty($data)){
                $projectIterator = new \ArrayIterator($data);
                foreach($projectIterator as $project){
                    $names[$index] = $project['name'];
                    $index++;
                    $project['account']=$account;
                    $projects[] = $project;
                }
            }
        }
        return $projects;
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