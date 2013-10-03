<?php
/**
 * Db
 * Instantiate Db Connection
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace Blueridge\Middleware;

use \Slim\Middleware;
use \Doctrine\MongoDB\Connection;
use \Doctrine\ODM\MongoDB\Configuration;
use \Doctrine\ODM\MongoDB\DocumentManager;
use \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

class Db extends Middleware
{
    public function call()
    {
        if(!empty($this->app->config('database')))
        {
            $this->app->container->singleton('dm', function () {
                $configs = $this->app->config('database');   

                if(empty($configs['user'])){
                    $connection_url = "mongodb://{$configs['host']}:{$configs['port']}/{$configs['name']}";    
                }else{
                    $connection_url = "mongodb://{$configs['user']}:{$configs['passwd']}@{$configs['host']}:{$configs['port']}/{$configs['name']}";   
                }                             
                AnnotationDriver::registerAnnotationClasses();

                $config = new Configuration();
                $config->setProxyDir(BIN_PATH.'/src/Blueridge/Documents/Proxies');
                $config->setProxyNamespace('Proxies');
                $config->setHydratorDir(BIN_PATH.'/src/Blueridge/Documents/Hydrators');
                $config->setHydratorNamespace('Hydrators');
                $config->setMetadataDriverImpl(AnnotationDriver::create(BIN_PATH.'/src/Blueridge/Documents'));
                $config->setDefaultDB($configs['name']);            
                return  DocumentManager::create( new Connection($connection_url), $config);                
            });   
        }
        $this->next->call();
    }
}