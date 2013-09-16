<?php
/**
 * Cache
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace Blueridge\Middleware;

use \Memcache;
use \Slim\Middleware;
use \Doctrine\Common\Cache\ApcCache;
use \Doctrine\Common\Cache\MemcacheCache;


class Cache extends Middleware
{
    public function call()
    {

        $memcache = new \Memcache();
        $memcache->connect('localhost', 11211);

        $cacheDriver = new MemcacheCache();
        $cacheDriver->setMemcache($memcache);

        // $cacheDriver = new ApcCache();
        $cacheDriver->setNamespace('blrg_sdk_');

        $this->app->cacheDriver = $cacheDriver;
        $this->next->call();
    }
}