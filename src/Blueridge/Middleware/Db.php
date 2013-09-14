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

                if(APPLICATION_ENV == 'development'){
                    $connection_url = "mongodb://{$configs['host']}:{$configs['port']}/{$configs['name']}";    
                }else{
                    $connection_url = "mongodb://{$configs['user']}:{$configs['passwd']}@{$configs['host']}:{$configs['port']}/{$configs['name']}";   
                }                             
                AnnotationDriver::registerAnnotationClasses();

                $config = new Configuration();
                $config->setProxyDir(APPLICATION_PATH.'/src/BlueRidge/Documents/proxies');
                $config->setProxyNamespace('Proxies');
                $config->setHydratorDir(APPLICATION_PATH.'/src/BlueRidge/Documents/hydrators');
                $config->setHydratorNamespace('Hydrators');
                $config->setMetadataDriverImpl(AnnotationDriver::create(APPLICATION_PATH.'/src/BlueRidge/Documents'));
                $config->setDefaultDB($configs['name']);            

                return  DocumentManager::create( new Connection($connection_url), $config);
                
            });
}
$this->next->call();
}
}