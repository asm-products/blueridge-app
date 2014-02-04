<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Providers\Basecamp;

use Guzzle\Http\Client;
use Guzzle\Common\Event;

class OAuth
{
    /**
     * Guzzle HTTP Client
     * @var Guzzle\Http\Client
     */
    protected $client;

    /**
     * Configuration settings from Mapster configs
     * @var Array
     */
    protected $configs;

    public function __construct(Array $configs)
    {
        $this->configs = $configs;        
        $this->client = new Client();
        $this->client->setUserAgent($this->configs['user_agent']);
    }

    public function authorize($app)
    {

        $params = [
        'client_id' => $this->configs['client_id'],
        'redirect_uri' => $this->configs['redirect_uri'],          
        'type' => $this->configs['type'],            
        ];

        $url= $this->configs['auth_url'].'?'.http_build_query($params);
        $app->redirect($url);
    }

    /**
     * Request an authorization token
     * 
     * @param  string $code code provided by the initial OAUTH request
     * @return Array      authorization token
     */
    public function getAccessToken($code)
    {
        $params = [
        'client_id'=>$this->configs['client_id'],
        'redirect_uri'=>$this->configs['redirect_uri'],
        'client_secret'=>$this->configs['client_secret'],
        'code'=>$code,
        'type'=>$this->configs['type']
        ];
        return $this->client->post($this->configs['token_url'],[],$params)->send()->json();  

    }

    public  function getAuthorization($token)
    {
        $this->setAuthHeader($token);
        $endpoint="https://launchpad.37signals.com/authorization.json";     
        $auth = $this->client->get($endpoint)->send()->json();

        $authorization=[
        'identity' => $auth['identity'],
        'expires_at' => $auth['expires_at']
        ];

        $authIterator = new \ArrayIterator($auth['accounts']);        
        foreach ($authIterator as $account) {
            if($account['product'] == 'bcx') {
            // if($account['product'] !='campfire' && $account['product'] !='skybox'){
                $authorization['accounts'][] = $account;
            }
        }       
        return $authorization;
    }

    /**
     * Return the current users information 
     * 
     * @param  Array $authorization
     * @return Array
     */
    public function getUserDetails(Array $authorization)
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
     * Sets the authorization header provided by the token 
     * 
     * @param Array token Array with token info
     * @return Void
     */
    protected function setAuthHeader(Array $token)
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

}