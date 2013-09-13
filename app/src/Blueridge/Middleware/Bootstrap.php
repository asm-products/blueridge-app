<?php
/**
 * Bootstrap
 * Instantiate application
 * @author Moses Ngone <mo@mospired.com>
 */

namespace Blueridge\Middleware;

use \Slim\Middleware;

class Bootstrap extends Middleware
{
    public function call()
    {
        $appEnv= APPLICATION_ENV;
        $config_file = APPLICATION_PATH."/configs/{$appEnv}.json";
        $config_content= file_get_contents($config_file);
        $configs =json_decode($config_content,true); 
        $configs['mode']=$appEnv;
        $this->app->config($configs);
        $this->next->call();
    }    
}