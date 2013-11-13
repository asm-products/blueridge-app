<?php
/**
 * Blueridge
 * 
 */

namespace Blueridge;

use Pimple;
use Memcache;
use Mandrill;
use IronMQ;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Cache\MemcacheCache;

use Zend\Cache\StorageFactory;




class Blueridge extends Pimple
{
    public function __construct()
    {
        parent::__construct();

        $reflection = new \ReflectionClass(__NAMESPACE__ . '\Blueridge');

        $methods = $reflection -> getMethods();        
        foreach ($methods as $method) {
            if (strpos($method -> name, '_init') !== false) {
                $method_name = $method -> name;
                $this -> $method_name();
            }
        }
    }

    /**
     * Init Config
     */
    public function _initConfig()
    {
        $appEnv= APPLICATION_ENV;
        $config_file = BIN_PATH."/configs/{$appEnv}.json";
        $config_content= file_get_contents($config_file);
        $configs =json_decode($config_content,true); 
        $configs['mode']=$appEnv;
        $this['configs']=$configs;

    }

    /**
     * Init Db
     */
    public function _initDb()
    {
        $container = $this;
        $this['documentManager'] =  $this->share(function () use ($container) {

            $dbConfigs = $container['configs']['database'];        
            if(!empty($dbConfigs))
            {
                if(empty($dbConfigs['user'])){
                    $connection_url = "mongodb://{$dbConfigs['host']}:{$dbConfigs['port']}/{$dbConfigs['name']}";    
                }else{
                    $connection_url = "mongodb://{$dbConfigs['user']}:{$dbConfigs['passwd']}@{$dbConfigs['host']}:{$dbConfigs['port']}/{$dbConfigs['name']}";   
                }

                AnnotationDriver::registerAnnotationClasses();

                $config = new Configuration();
                $config->setProxyDir(BIN_PATH.'/src/Blueridge/Documents/Proxies');
                $config->setProxyNamespace('Proxies');
                $config->setHydratorDir(BIN_PATH.'/src/Blueridge/Documents/Hydrators');
                $config->setHydratorNamespace('Hydrators');
                $config->setMetadataDriverImpl(AnnotationDriver::create(BIN_PATH.'/src/Blueridge/Documents'));
                $config->setDefaultDB($dbConfigs['name']);
                return DocumentManager::create( new Connection($connection_url), $config);

            }
            return;
        });
}

    /**
     * Init File Cache
     */
    public function _initFileCache()
    {

        $this['fileCache'] =  $this->share(function (){
            $fileCache  = StorageFactory::adapterFactory('filesystem', ['ttl' => 3600,'cache_dir'=>CACHE_DIR.'/data']);        
            $plugin = StorageFactory::pluginFactory('exception_handler',['throw_exceptions' => false]);
            $fileCache->addPlugin($plugin);
            return $fileCache;
        });
    }


    /**
     * Init Mail Transaction Service
     */
    public function _initMailTransactionService()
    {
        $container = $this;
        $this['mailService'] =  $this->share(function () use ($container) {

            $mailConfigs = $container['configs']['services']['mail'];         
            if(!empty($mailConfigs))
            {
                return new Mandrill($mailConfigs['api_key']);
            }
            return;
        });
    }

}