<?php
/**
 * Script for creating and loading database
 */

// Initialize the application path and autoloading

define ('DEFAULT_ADMIN', 'admin');
define ('DEFAULT_PASSWORD', 'admin');

defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

$dbparams = $config->resources->db->params;


// let the user know whats going on (we are actually creating a
// database here)

echo 'Writing Database in (control-c to cancel): ' . PHP_EOL;
for ($x = 3; $x > 0; $x--) {
    echo $x . "\r"; sleep(1);
}


// this block executes the actual statements that were loaded from
// the schema file.
try {
    $schemaSql = file_get_contents(dirname(__FILE__) . '/schema.mysql.sql');
    // use the connection directly to load sql in batches

    $mysqli = new mysqli($dbparams->host, $dbparams->username, $dbparams->password);

    $mysqli->query("create database $dbparams->dbname");
    $mysqli->select_db($dbparams->dbname);

    $mysqli->multi_query($schemaSql);

    $mysqli->close();


    echo PHP_EOL;
    echo 'Database Created';
    echo PHP_EOL;
    echo 'Login with superuser: admin@products-pilot.loc and password: admin';
    echo PHP_EOL;


} catch (Exception $e) {
    echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    return false;
}

// generally speaking, this script will be run from the command line
return true;