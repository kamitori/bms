<?php

/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
// Setup a 'default' cache configuration for use in the application.
if(class_exists('Memcache'))
    Cache::config('default', array(
        'engine' => 'Memcache',
        'duration' => 604800,
        'probability' => 100,
        'prefix' => Inflector::slug(APP_DIR) . '_',
        'servers' => array(
            '127.0.0.1:11211'
        ),
        'persistent' => true,
        'compress' => false,
    ));
else{
    Cache::config('default', array(
        'engine' => 'File', //[required]
        'duration' => 604800, //[optional]
        'probability' => 100, //[optional]
    ));
}
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models', '/next/path/to/models'),
 *     'Model/Behavior'            => array('/path/to/behaviors', '/next/path/to/behaviors'),
 *     'Model/Datasource'          => array('/path/to/datasources', '/next/path/to/datasources'),
 *     'Model/Datasource/Database' => array('/path/to/databases', '/next/path/to/database'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions', '/next/path/to/sessions'),
 *     'Controller'                => array('/path/to/controllers', '/next/path/to/controllers'),
 *     'Controller/Component'      => array('/path/to/components', '/next/path/to/components'),
 *     'Controller/Component/Auth' => array('/path/to/auths', '/next/path/to/auths'),
 *     'Controller/Component/Acl'  => array('/path/to/acls', '/next/path/to/acls'),
 *     'View'                      => array('/path/to/views', '/next/path/to/views'),
 *     'View/Helper'               => array('/path/to/helpers', '/next/path/to/helpers'),
 *     'Console'                   => array('/path/to/consoles', '/next/path/to/consoles'),
 *     'Console/Command'           => array('/path/to/commands', '/next/path/to/commands'),
 *     'Console/Command/Task'      => array('/path/to/tasks', '/next/path/to/tasks'),
 *     'Lib'                       => array('/path/to/libs', '/next/path/to/libs'),
 *     'Locale'                    => array('/path/to/locales', '/next/path/to/locales'),
 *     'Vendor'                    => array('/path/to/vendors', '/next/path/to/vendors'),
 *     'Plugin'                    => array('/path/to/plugins', '/next/path/to/plugins'),
 * ));
 *
 */
/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */
/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */
CakePlugin::loadAll();
CakePlugin::load(array('Minify' => array('routes' => true)));
/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 * 		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 * 		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 * 		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
    'AssetDispatcher',
    'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
    'engine' => 'FileLog',
    'types' => array('notice', 'info', 'debug'),
    'file' => 'debug',
));
CakeLog::config('error', array(
    'engine' => 'FileLog',
    'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
    'file' => 'error',
));

// CakePlugin::load('Mongodb');
// BaoNam
define('EMAIL_WHEN_ERROR', 'vu.nguyen@anvydigital.com');
define('DEFAULT_LANG', 'en');
define('KEY_LANG', 'JT_');
// define('URL_HOME','/');
$check_permission = false;
$is_local = false;
$is_new_code = false;
$socket_url = false;
if(!isset($_SERVER['SERVER_NAME'])){
    define('URL', 'http://jt.banhmisub.com');
    define('IP_CONNECT_MONGODB', '127.0.0.1');
    define('PHANTOMJS_PATH', WWW_ROOT . 'phantomjs' . DS);
    define('DB_CONNECT_MONGODB', 'bms');
    $check_permission = true;
    $is_new_code = true;
    $socket_url = '205.206.177.129';
} else if ($_SERVER['SERVER_NAME'] == 'localhost') {
	define('URL', 'http://localhost');
	define('IP_CONNECT_MONGODB', 'localhost');
	define('DB_CONNECT_MONGODB', 'bms');
    define('PHANTOMJS_PATH', WWW_ROOT . 'phantomjs-windows' . DS);
    $check_permission = true;
    $is_local = true;
    $is_new_code = true;

} elseif ($_SERVER['SERVER_NAME'] == 'jt.banhmisub.com') {
    define('URL', 'http://jt.banhmisub.com');
    define('IP_CONNECT_MONGODB', '127.0.0.1');
    define('PHANTOMJS_PATH', WWW_ROOT . 'phantomjs' . DS);
    define('DB_CONNECT_MONGODB', 'bms');
    $check_permission = true;
    $is_new_code = true;
    $socket_url = '205.206.177.129';

} elseif ($_SERVER['SERVER_NAME'] == 'demo.jobtraq.vimpact.ca') {
    define('URL', 'http://demo.jobtraq.vimpact.ca');
    define('IP_CONNECT_MONGODB', '127.0.0.1');
    define('PHANTOMJS_PATH', WWW_ROOT . 'phantomjs' . DS);
    define('DB_CONNECT_MONGODB', 'bmsdemo');
    $check_permission = true;
    $is_new_code = true;
    $socket_url = '205.206.177.129';

} else if ($_SERVER['SERVER_NAME'] == 'bms.com') {
    define('URL', 'http://bms.com');
    define('IP_CONNECT_MONGODB', '127.0.0.1');
    define('PHANTOMJS_PATH', WWW_ROOT . 'phantomjs-windows' . DS);
    define('DB_CONNECT_MONGODB', 'bms');
    $check_permission = true;
    $is_local = true;
    $is_new_code = true;

} elseif ($_SERVER['SERVER_NAME'] == 'bmsjt.vimpact.ca') {
    define('URL', 'http://bmsjt.vimpact.ca');
    define('IP_CONNECT_MONGODB', '127.0.0.1');
    define('PHANTOMJS_PATH', WWW_ROOT . 'phantomjs' . DS);
    define('DB_CONNECT_MONGODB', 'bmstest');
    $check_permission = true;
    $is_new_code = true;
    $socket_url = '205.206.177.129';

} else if ($_SERVER['SERVER_NAME'] == 'bmsdemo_jt.com') {
    define('URL', 'http://bmsdemo_jt.com');
    define('IP_CONNECT_MONGODB', '127.0.0.1');
    define('PHANTOMJS_PATH', WWW_ROOT . 'phantomjs-windows' . DS);
    define('DB_CONNECT_MONGODB', 'bms');
    $check_permission = true;
    $is_local = true;
    $is_new_code = true;

}else {
    define('URL', '');
    define('IP_CONNECT_MONGODB', '127.0.0.1');
    define('DB_CONNECT_MONGODB', 'bms');
}
define('IS_LOCAL', $is_local);
define('IS_NEW_CODE', $is_new_code);
define('SOCKET_URL', $socket_url);
define('M_URL',URL.'/mobile');
function msg($msg) {
    if (isset($_SESSION['arr_messages'][$msg]))
        echo $_SESSION['arr_messages'][$msg];
    else
        echo '';
}

define('LIMIT_PRINT_PDF',100);
define('LIST_LIMIT', 100);
define('COMBOBOX_SEPARATE', '_jt@_');
define('LOAD_MORE', 30);
define('CHECK_DB_PRIVILEGE', true);

define('CHECK_PERMISSION',$check_permission);
// yum install sendmail (CentOS)
// apt-get install sendmail (Ubuntu)

// mongodump --db jobtraq_dev
// mongorestore --db jobtraq dump/jobtraq_dev