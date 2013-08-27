<?php
/**
 * Configs
 * Instantiate application configs
 * @author Moses Ngone <mo@mospired.com> @mospired
 */

namespace BlueRidge\Init;

use \Slim\Middleware;
use \Slim\Views\Twig;
use \Slim\Views\TwigExtension;

class Configs extends Middleware
{
    /**
     * Call
     * Middleware call
     */
    public function call()
    {

        $appEnv= APPLICATION_ENV;
        $config_file = APPLICATION_PATH."/configs/{$appEnv}.json";
        $config_content= file_get_contents($config_file);
        $configs =json_decode($config_content,true); 
        $configs['mode']=$appEnv;
        $configs['view']= new Twig();
        $configs['templates.path'] = APPLICATION_PATH.'/resources/views';
        $this->app->config($configs); 

        // set Twig Extensions
        $this->app->view->parserOptions = ['debug' => true,'cache' => CACHE_DIR.'/front'];
        $this->app->view->parserExtensions = [ new TwigExtension()];


        $this->next->call();
        
    }

    
}