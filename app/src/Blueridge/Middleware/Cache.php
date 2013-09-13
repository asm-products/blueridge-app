<?php
/**
 * Cache
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace BlueRidge\Middleware;

use \Slim\Middleware;
use \Doctrine\Common\Cache\ApcCache;

class Cache extends Middleware
{
    public function call()
    {
        $this->app->cache = new ApcCache();
        $this->next->call();
    }
}