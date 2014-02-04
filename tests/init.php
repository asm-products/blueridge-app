<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 * @since v1.1.0
 */

/**
 * Tests bootstrap
 */

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

defined('APPLICATION_ROOT') || define('APPLICATION_ROOT', realpath(dirname(__FILE__).'/../'));
defined('CACHE_DIR') || define('CACHE_DIR', APPLICATION_ROOT.'/cache');
defined('APP_PATH') || define('APP_PATH', APPLICATION_ROOT.'/app');
defined('BIN_PATH') || define('BIN_PATH', APPLICATION_ROOT.'/bin');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$loader = require APPLICATION_ROOT.'/vendor/autoload.php';
$loader->add('src\\Bluridge\\Tests\\',APPLICATION_ROOT.'/tests');
define('SLIM_MODE', 'testing');