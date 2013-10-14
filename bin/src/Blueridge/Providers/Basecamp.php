<?php
/**
 * Basecamp Client
 */

namespace Blueridge\Providers;

use \Doctrine\Common\Cache\MemcacheCache;
use \Doctrine\Common\Cache\FilesystemCache;

use \Guzzle\Http\Client;
use \Guzzle\Common\Event;

use \Guzzle\Cache\DoctrineCacheAdapter;
use \Guzzle\Plugin\Cache\DefaultCacheStorage;
use \Guzzle\Plugin\Cache\CachePlugin;


class Basecamp
{    

    protected $client;
    protected $configs;
    protected $app;

    /**
     * Basecamp
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->configs = $app->config('providers')['basecamp']; 
        $this->client = new Client('https://basecamp.com/');

        $this->client->setUserAgent($this->configs['user_agent']);

        $cachePlugin = new CachePlugin(array(
            'storage' => new DefaultCacheStorage(
                new DoctrineCacheAdapter(
                    new FilesystemCache(CACHE_DIR.'/back')
                    )
                )
            ));
        $this->client->addSubscriber($cachePlugin);
    }

    /**
     * Get Todolist
     */
    public function getTodolists(\Blueridge\Documents\User $user)
    {
        $todolists = [];
        $projectIterator = new \ArrayIterator($user->projects);

        $this->setAuth($user);

        foreach ($projectIterator as $project) {
            if($project['selected'])
            {                
                $endpoint = "{$project['account']['href']}/projects/{$project['id']}/todolists.json";
                
                $list = $this->client->get($endpoint)->send()->json();

                array_walk($list, function(&$a, $key, $project) {
                    $a['parent'] = ['account_name'=>$project['account']['name'],'project_name'=> $project['name']];                    
                },$project);

                $todolists = array_merge($todolists,$list);
            }

        }
        return $todolists;

    }

    /**
     * Get Todos
     */
    public function getTodos(\Blueridge\Documents\User $user)
    {
        $todos = [];
        $todolists = $this->getTodolists($user);

        if (empty($todolists))
        {
            return;
        }


        $todolistIterator = new \ArrayIterator($todolists); 

        foreach ($todolistIterator as $todolist) {

            $bc_todolist = $this->client->get($todolist['url'])->send()->json();

            $list = $bc_todolist['todos']['remaining'];                
            $todolist['parent']['list_name'] = $bc_todolist['name'];

            array_walk($list, function(&$a, $key, $parent) {             
                $a['parent'] = $parent;                    
                $a['href'] = $this->getSiteUrl($a['url']);               
            },$todolist['parent']);
            
            $todos = array_merge($todos,$list);            
        }
        
        return $this->organizeTodos($todos);

    }

    /**
     * Organize Todoss
     */
    protected function organizeTodos($todoItems)
    {
        if(empty($todoItems))
        {
            return;
        }

        $todos = [];
        $todosIterator = new \ArrayIterator($todoItems);

        foreach($todosIterator as $key => $todo){
            $todo['overdue_by'] = 0;
            $now= new \DateTime('now');

            if(!empty($todo['due_on'])){            
                $due_on= new \DateTime($todo['due_on']);                
                $todo['due_date']=$due_on->getTimestamp();
                if($now > $due_on){
                    $todo['overdue_by']= $due_on->diff($now, true)->format('%a');
                }

            }else{                
                $todo['due_date']=$now->add(new \DateInterval('P6Y'))->getTimestamp();
            }

            if(empty($todo['assignee']))
            {
                $todo['assignee'] = ['id'=>null,'type'=>'Person','name'=>'Unassigned'];
            }

            $todos[]=$todo;
        }
        return $todos;  

    }

    /**
     * Set Auth
     */
    protected function setAuth(\Blueridge\Documents\User $user)
    {
        if(!empty($user))
        {
            $this->configs = array_merge($this->configs,$user->providers['basecamp']['token']);

            if(!empty($this->configs['access_token']))
            {
                $authorization = sprintf('Bearer %s', $this->configs['access_token']);

                $this->client->getEventDispatcher()->addListener('request.before_send', function(Event $event) use ($authorization) {
                    $event['request']->addHeader('Authorization', $authorization);

                });
            }  

        }  

    }

    /**
     * Get Site Url
     */
    protected function getSiteUrl($url){
        $points = ['/api/v1','.json'];
        $siteUrl = str_replace($points,'',$url);
        return $siteUrl;
    }
}