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
    public function call(){

        $this->setApplicationEnvironment();    
        $this->next->call();
    }

    /**
     * Setup Application
     */
    protected function setApplicationEnvironment(){ 

        $app_env = getenv('APPLICATION_ENV');
        $appEnv= ($app_env)?$app_env:"production";  
        $configs = $this->getConfigs($appEnv); 
        
        $appSettings = ["mode"=>$appEnv,"debug"=>$configs->debug,"log.level"=>$configs->log_enabled,"templates.path"=>$configs->templates_path];
        $this->app->config($appSettings);
        
        if($configs->database){
            $this->app->database = $this->setDbAdapter($configs->database);
        }

        if($configs->cache){
            $this->app->cache = $this->setCache($configs->cache);
        }

        if($configs->services){
            $this->app->services = $configs->services;
        }

        $this->app->resource = $this->setResource();
    }

    /**
     * Get Application Settings
     */
    protected function getConfigs($appEnv){

        $config_file = "../api/configs/{$appEnv}.json";
        $config_content= file_get_contents($config_file); 
        return $configs =json_decode($config_content);   

    }

    /**
     * Setup Database Adapter
     */
    protected function setDbAdapter($db){

        $client= new \MongoClient("mongodb://{$db->user}:{$db->passwd}@{$db->host}:{$db->port}/{$db->name}");

        return $client->selectDB($db->name);
    }

    /**
     * Setup Cache
     */
    protected function setCache(){
       // return;
        $memcache = new \Memcache;
        $connection= $memcache->connect('localhost', 11211);

        if(empty($connection)){
            return;
        }

        return $memcache;

    }

    /**
     * Setup Routes and Requested Resource
     */
    protected function setResource(){

        $path = $this->app->request()->getPath();
        list($separator,$root,$base) = explode('/', $path) + array(
          null,
          null,
          );

        $jsonRoutes= file_get_contents("../api/configs/routes.json");    
        $routes =json_decode($jsonRoutes); 

        if($base ==''){
            $base = 'home';
        }

        if(!array_key_exists($base, $routes)){
         return;
     }

     return $resource = $routes->$base;
 }
}