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
        $this['configs']=$configs;
    }

    /**
     * Connect to MongoDb
     * @return DocumentManager
     */
    public function _initDb()
    {
        $container = $this;
        $this['documentManager'] =  $this->share(function () use ($container) {
            $dbConfigs = $container['configs']['database'];
            try{
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

            }catch(Exception $e){
                error_log($e->getMessage());
            } 
        });
    }

    /**
     * Setup APC cache
     * @return Zend\Cache\Manager
     */
    public function _initCache()
    {
        $container = $this;
        $this['cacheManager'] =  $this->share(function () use ($container) {
            $cacheConfigs = $container['configs']['cache'];
            switch ($cacheConfigs['storage']) {
                case 'apc':
                    $cache  = StorageFactory::adapterFactory('apc', ['ttl' => $cacheConfigs['ttl']]);
                    break;

                case 'memcached':
                    $cache  = StorageFactory::adapterFactory('memcached', ['ttl' => $cacheConfigs['ttl']]);
                    break;

                default:
                    $cache  = StorageFactory::adapterFactory('filesystem', ['ttl' => $cacheConfigs['ttl'],'cache_dir'=>CACHE_DIR.'/sessions']);
                    break;
            }
            
            $plugin = StorageFactory::pluginFactory('exception_handler',['throw_exceptions' => true]);
            $cache->addPlugin($plugin);
            return $cache;
        });
    }


    /**
     * Initailaize Mandrill for mail transactions
     * @return Mandrill
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
     * Create a session manager using the cache manager as storage
     * @return Zend\Session\Manager
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
     * Setup an authentication service
     * @return Zend\Authentication\AuthenticationService
     */
    public function _initAuthenticationService()
    {
        $container = $this;

        $this['authenticationService'] = $this->share( function () use ($container){
            $sessionStorage = new SessionStorage('Blueridge','storage', $container['sessionManager']);
            $auth= new AuthenticationService();
            return $auth->setStorage($sessionStorage);        
        });
    }
}