<?php
/**
 * Basecamp Client
 */

namespace Blueridge\Providers;

use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Http\Client;
use Guzzle\Common\Event;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\DefaultCacheStorage;
use Guzzle\Plugin\Cache\CachePlugin;


class Basecamp
{

    protected $client;
    protected $configs;
    protected $blueridge;

    /**
     * Basecamp
     */
    public function __construct($blueridge)
    {
        $this->blueridge = $blueridge;
        $this->configs = $blueridge['configs']['providers']['basecamp'];
        $this->client = new Client('https://basecamp.com/',['params.cache.override_ttl' => 3600]);

        $this->client->setUserAgent($this->configs['user_agent']);

        $cachePlugin = new CachePlugin(array(
            'storage' => new DefaultCacheStorage(
                new DoctrineCacheAdapter(
                    new FilesystemCache(CACHE_DIR.'/clients/basecamp')
                    )
                )
            ));
        $this->client->addSubscriber($cachePlugin);
    }

    /**
     * Get Token
     */
    public function getToken($code)
    {
        $params = [
        'type'=>'web_server',
        'client_id'=>$this->configs['client_id'],
        'redirect_uri'=>$this->configs['redirect_uri'],
        'client_secret'=>$this->configs['client_secret'],
        'code'=>$code
        ];
        return $this->client->post($this->configs['token_url'],[],$params)->send()->json();

    }


    /**
    * Get Authorization
    */

    public  function getAuthorization($token)
    {
        $this->setAuth($token);
        $endpoint="https://launchpad.37signals.com/authorization.json";
        $auth = $this->client->get($endpoint)->send()->json();

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
    public function getMe($authorization)
    {

        $endpoint = "people/me.json";
        $url="{$authorization['accounts'][0]['href']}/{$endpoint}";

        $whoami = $this->client->get($url)->send()->json();
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
    public function getProjects(\Blueridge\Documents\User $user)
    {

        $endpoint="projects.json";
        $projects=[];
        $index=0;


        $this->setAuth($user->providers['basecamp']['token']);


        $accountIterator = new \ArrayIterator($user->providers['basecamp']['accounts']);
        foreach ($accountIterator as $account) {

            try {
                $list = $this->client->get("{$account['href']}/{$endpoint}")->send()->json();
            } catch(\Exception $e) {
                error_log($e->getMessage());
                $list = null;
            }

            if(!empty($list)){
                array_walk($list, function(&$project, $key, $account) {
                    $project['account'] = $account;
                },$account);

                $projects = array_merge($projects,$list);
            }
        }
        return $projects;
    }




    /**
     * Get Todolist
     */
    public function getTodolists(\Blueridge\Documents\User $user, Array $projects = null)
    {

        $todolists = [];
        $projectIterator = new \ArrayIterator($user->projects);

        $this->setAuth($user->providers['basecamp']['token']);

        foreach ($projectIterator as $project) {

            if(!empty($projects)){
                if(in_array($project['id'], $projects)){
                    $endpoint = "{$project['account']['href']}/projects/{$project['id']}/todolists.json";
                    $list = $this->client->get($endpoint)->send()->json();
                    array_walk($list, function(&$a, $key, $project) {
                        $a['rel']['project'] = $project;
                    },$project);
                    $todolists = array_merge($todolists,$list);
                }
            }else{
                $endpoint = "{$project['account']['href']}/projects/{$project['id']}/todolists.json";
                $list = $this->client->get($endpoint)->send()->json();
                array_walk($list, function(&$a, $key, $project) {
                    $a['rel']['project'] = $project;
                },$project);
                $todolists = array_merge($todolists,$list);
            }


        }

        return $todolists;

    }

    /**
     * Get Todos
     */
    public function getTodos(\Blueridge\Documents\User $user, Array $projects= null)
    {
        $todos = [];
        $todolists = $this->getTodolists($user, $projects);

        if (empty($todolists))
        {
            return;
        }

        $todolistIterator = new \ArrayIterator($todolists);

        foreach ($todolistIterator as $todolist) {

            $bc_todolist = $this->client->get($todolist['url'])->send()->json();
            $list = $bc_todolist['todos']['remaining'];

            $rel=['project'=>$todolist['rel']['project'],'list_name'=>$bc_todolist['name']];
            array_walk($list, function(&$a, $key, $rel) {
                $a['rel'] = $rel;
                $a['rel']['href'] = $this->getSiteUrl($a['url']);
            },$rel);

            $todos = array_merge($todos,$list);

        }
        return $todos;
    }

    /**
     * getTodo Get Todo
     * @param  BlueridgeDocumentsUser $user
     * @param  String                 $url
     * @return Object
     */
    public function getTodo(\Blueridge\Documents\User $user, $url)
    {
        try{
            $this->setAuth($user->providers['basecamp']['token']);
            return $this->client->get($url)->send()->json();
        }catch(\Exception $e){
            return;
        }

    }

    /**
     * [completeTodo description]
     * @param  BlueridgeDocumentsUser $user [description]
     * @return [type]                       [description]
     */
    public function updateTodo(\Blueridge\Documents\User $user,\Blueridge\Documents\Todo $todo,$payload)
    {
        try{
            $this->setAuth($user->providers['basecamp']['token']);
            $data = [
            'content'=>$todo->source['content'],
            'due_at'=>(empty($todo->source['due_at']))?false:$todo->source['due_at'],
            'assignee'=>(empty($todo->source['assignee']))?null:$todo->source['assignee'],
            'completed'=>(empty($todo->source['completed']))?false:$todo->source['completed']
            ];

            foreach ($payload as $key => $value) {
                if(array_key_exists($key, $data)){
                    $data[$key]=$value;
                }
            }
            return $this->client->put($todo->source['url'],['Content-Type'=>'application/json'],json_encode($data))->send()->getStatusCode();
        }catch(\Exception $e){
            error_log($e->getMessage());
        }

    }


    /**
     * Set Auth
     */
    protected function setAuth($token)
    {

        $this->configs = array_merge($this->configs,$token);

        if(!empty($this->configs['access_token']))
        {
            $authorization = sprintf('Bearer %s', $this->configs['access_token']);

            $this->client->getEventDispatcher()->addListener('request.before_send', function(Event $event) use ($authorization) {
                $event['request']->addHeader('Authorization', $authorization);

            });
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