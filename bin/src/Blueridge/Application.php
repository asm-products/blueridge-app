<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge;

use Pimple;
use Memcache;
use Mandrill;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Cache\MemcacheCache;
use Zend\Cache\StorageFactory;
use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

/**
 * Blueridge 
 */
class Application extends Pimple
{
    public function __construct()
    {
        parent::__construct();

        $reflection = new \ReflectionClass(__NAMESPACE__ . '\Application');

        $methods = $reflection -> getMethods();        
        foreach ($methods as $method) {
            if (strpos($method -> name, '_init') !== false) {
                $method_name = $method -> name;
                $this -> $method_name();
            }
        }
    }

    /**
     * Application Config
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
     * Document Manager
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
     * Cache Manager
     */
    public function _initCache()
    {

        $this['cacheManager'] =  $this->share(function (){

            $fileCache  = StorageFactory::adapterFactory('filesystem', ['ttl' => 3600,'cache_dir'=>CACHE_DIR.'/data']);        
            $plugin = StorageFactory::pluginFactory('exception_handler',['throw_exceptions' => false]);
            $fileCache->addPlugin($plugin);

            return $fileCache;
        });
    }


    /**
     * Mail Transaction Service
     */
    public function _initMailTransactionService()
    {
        $container = $this;
        $this['mailService'] =  $this->share(function () use ($container) {

            $mailConfigs = $container['configs']['services']['mail']['mandrill'];         
            if(!empty($mailConfigs))
            {
                return new Mandrill($mailConfigs['api_key']);
            }
            return;
        });
    }

    /**
     * Session Manager
     */
    public function _initSessionManager()
    {
        $container = $this;

        $this['sessionManager'] = $this->share( function () use ($container){

            $sessionConfigs = $container['configs']['session'];
            $saveHandler = new Cache($container['cacheManager']);

            $config = new SessionConfig();
            $config->setOptions($sessionConfigs);
            $manager= new SessionManager($config);
            $manager->setSaveHandler($saveHandler);
            Container::setDefaultManager($manager);
            return $manager;
        });
    }

    /**
    * Authentication service
    */
    public function _initAuthenticationService()
    {
        $container = $this;

        $this['authenticationService'] = $this->share( function () use ($container){
            $auth = new AuthenticationService();            
            return $auth->setStorage(new SessionStorage('Blueridge'));        
        });
    }
}