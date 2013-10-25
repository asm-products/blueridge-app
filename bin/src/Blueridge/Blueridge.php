<?php
/**
 * Blueridge
 * 
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
        $dbConfigs = $this['configs']['database'];        
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
            $this['documentManager']= DocumentManager::create( new Connection($connection_url), $config);

        }

    }


    public function _initFileCache()
    {

        // $frontendOptions = array('lifetime' => 864000, 'automatic_serialization' => true); // 24 hours cache
        // $backendOptions = array('cache_dir' => CACHE_DIR);
        // $cache = Zend_Cache::factory('Core','File',$frontendOptions,$backendOptions);

    }


    public function _initMailTransactionService()
    {
        $mailConfigs = $this['configs']['services']['mail'];         
        if(!empty($mailConfigs))
        {
            $this['mailService'] = new Mandrill($mailConfigs['api_key']);
        }
    }


    public function _initBasecampClient()
    {
       // $clientConfig = new ClientConfig(
       //  [
       //  'authorize_endpoint' => $settings['auth_url'],
       //  'client_id' => $settings['client_id'],
       //  'client_secret' => $settings['client_secret'],
       //  'token_endpoint' => $settings['token_url'],
       //  'redirect_uri' => $settings['redirect_uri'],
       //  'enable_debug' => true
       //  ]
       //  );

    }

}