<?php
/**
 * Init
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace BlueRidge;

use \Slim\Middleware;

class Init extends Middleware
{
    protected $configs;
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


        $appEnv= APPLICATION_ENV;
        $this->configs = $this->getConfigs($appEnv); 
        
        $appSettings = ["mode"=>$appEnv,"debug"=>$this->configs->debug,"log.level"=>$this->configs->log_enabled];
        $this->app->config($appSettings);
        
        if($this->configs->database){
            $this->app->container->singleton('database', function(){
                $db = $this->configs->database;

                $connection_url = "mongodb://{$db->host}:{$db->port}/{$db->name}";   
                if(isset($db->user) && isset($db->passwd)){
                    $connection_url = "mongodb://{$db->user}:{$db->passwd}@{$db->host}:{$db->port}/{$db->name}";
                }

                $client= new \MongoClient($connection_url);
                return $client->selectDB($db->name);
            });
        }

        if($this->configs->providers){
            $this->app->providers = $this->configs->providers;
        }


        if($this->configs->services->mail){

            $this->app->container->singleton('mailbox',function(){
                return $transport = new \Mandrill($this->configs->services->mail->api_key);
            });

        }    

        if($this->configs->services->subscriber){
            $this->app->subscriber = $this->configs->services->subscriber;
        }

    }

    /**
     * Get Application Settings
     */
    protected function getConfigs($appEnv)
    {

        $config_file = APPLICATION_PATH."/configs/{$appEnv}.json";
        $config_content= file_get_contents($config_file);

        return $configs =json_decode($config_content);   
    }
    
}