<?php

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('APPLOG_PATH') || define('APPLOG_PATH', realpath(APPLICATION_PATH . "/../../logs/"));
defined('LIBRARY_PATH') || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
include_once APPLICATION_PATH . '/configs/config.php';
define("ST_TIME", get_microtime());

// Define application environment
//defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
if (!in_array($_SERVER['SERVER_NAME'], array('online.manage.uenu.com', 'manage.uenu.com', 'manage.uenu.com.cn'))) {
    define('APPLICATION_ENV', 'production');
} elseif (in_array($_SERVER['SERVER_NAME'], array('qa.manage.uenu.com'))) {
    define('APPLICATION_ENV', 'testing');
} else {
    define('APPLICATION_ENV', 'development');
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/commons'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();
StElog::pushNotice("request_time", StElog::useTime());
StElog::pushNotice('finishStElog', 'ok');
StElog::buildNotice();
