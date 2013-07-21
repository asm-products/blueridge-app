<?php
/**
 * Init
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace BlueRidge;

use \Slim\Middleware;

class Init extends Middleware
{
    /**
     * Call
     * Middleware call
     */
    public function call()
    {

        $this->setApplicationEnvironment();    
        $this->next->call();
    }

    /**
     * Setup Application
     */
    protected function setApplicationEnvironment()
    { 

        $app_env = getenv('APPLICATION_ENV');
        $appEnv= ($app_env)?$app_env:"production";  
        $configs = $this->getConfigs($appEnv); 
        
        $appSettings = ["mode"=>$appEnv,"debug"=>$configs->debug,"log.level"=>$configs->log_enabled];
        $this->app->config($appSettings);
        
        if($configs->database){
            $this->app->database = $this->setDbAdapter($configs->database);
        }

        if($configs->cache){
            $this->app->cache = $this->setCache($configs->cache);
        }

        if($configs->providers){
            $this->app->providers = $configs->providers;
        }

        if($configs->mail){
            $this->app->mailbox = $this->setMailBox($configs->mail);
        }

    }

    /**
     * Get Application Settings
     */
    protected function getConfigs($appEnv)
    {

        $config_file = "../api/configs/{$appEnv}.json";
        $config_content= file_get_contents($config_file); 
        return $configs =json_decode($config_content);   

    }

    /**
     * Setup Database Adapter
     */
    protected function setDbAdapter($db)
    {
        $connection_url = "mongodb://{$db->host}:{$db->port}/{$db->name}";   
        if(isset($db->user) && isset($db->passwd)){
            $connection_url = "mongodb://{$db->user}:{$db->passwd}@{$db->host}:{$db->port}/{$db->name}";
        }

        $client= new \MongoClient($connection_url);
        return $client->selectDB($db->name);
    }

    /**
     * Setup Cache
     */
    protected function setCache()
    {

        $memcache = new \Memcache('localhost', 11211);
        return $memcache;

    }

    /**
     *  Setup Mail
     */
    protected function setMailBox($mailbox)
    {

        return $transport = new \Mandrill($mailbox->api_key);

    }
}