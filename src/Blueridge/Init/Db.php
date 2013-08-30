<?php
/**
 * Db
 * Instantiate Db Connection
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace BlueRidge\Init;

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
            $this->app->container->singleton('db', function () {
                $configs = $this->app->config('database');
                $connection_url = "mongodb://{$configs['host']}:{$configs['port']}/{$configs['name']}";
                AnnotationDriver::registerAnnotationClasses();

                $config = new Configuration();
                $config->setProxyDir(APPLICATION_PATH.'/src/BlueRidge/Documents/proxies');
                $config->setProxyNamespace('Proxies');
                $config->setHydratorDir(APPLICATION_PATH.'/src/BlueRidge/Documents/hydrators');
                $config->setHydratorNamespace('Hydrators');
                $config->setMetadataDriverImpl(AnnotationDriver::create(APPLICATION_PATH.'/src/BlueRidge/Documents'));
                $config->setDefaultDB($configs['name']);

                $client= new \MongoClient($connection_url);

                return  DocumentManager::create( new Connection($client), $config);
                
            });
        }
        $this->next->call();
    }
}