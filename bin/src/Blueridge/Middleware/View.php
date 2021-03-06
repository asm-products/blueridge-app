<?php
/**
 * View Config
 * Instantiate view
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace Blueridge\Middleware;

use Slim\Middleware;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig_Extension_Debug;

/**
 * View Middleware
 */
class View extends Middleware
{
    public function call()
    {
        $configs = [
        'view'=> new Twig(),
        'templates.path'=> APP_PATH.'/resources/views'
        ];
        $this->app->config($configs);
        $this->app->view->parserOptions = ['debug' => true,'cache' => CACHE_DIR.'/ui'];
        $this->app->view->parserExtensions = [ new TwigExtension(),new Twig_Extension_Debug()];
        $this->next->call();

    }
}