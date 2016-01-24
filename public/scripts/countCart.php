<?php

defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__)) . '/../application'));
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
)));


defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', 'production');


require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
$dbAdapter = $bootstrap->getResource('db');


if (isset($_POST['user_id'])) $user_id = $_POST['user_id'];
else return;
$quantity = $entry = $dbAdapter->fetchRow($dbAdapter->select()->from('shoppingcarts', array('total' => 'SUM(quantity)'))->where('user_id = ?', $user_id));

$nr_products = ($quantity['total']) ? $quantity['total'] : 0;

echo $nr_products;
