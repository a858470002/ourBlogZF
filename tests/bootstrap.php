<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
// defined('APPLICATION_ENV')
//     || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

require_once APPLICATION_PATH . '/models/Admin.php';
require_once APPLICATION_PATH . '/models/DbTable/User.php';
require_once APPLICATION_PATH . '/models/DbTable/Article.php';
$autoloader->registerNamespace('OurBlog_');
$db = new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'dbname'   => 'blog_zftest',
            'charset'  => 'utf8'
        ));
Zend_Db_Table_Abstract::setDefaultAdapter($db);
include __DIR__.'/Test/MyApp_DbUnit_ArrayDataSet.php';
