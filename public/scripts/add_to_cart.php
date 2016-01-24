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

/*
$query = "";

$dbAdapter->getConnection()->query($query);*/

if (isset($_POST['user_id'])) $user_id = $_POST['user_id'];
else return;
if (isset($_POST['product_id'])) $product_id = $_POST['product_id'];
else return;


$entry = $dbAdapter->fetchRow($dbAdapter->select('quantity')->from('shoppingcarts')->where('user_id = ?', $user_id)->where('product_id = ?', $product_id));

if($entry['quantity']) {
    $data = array(
        'quantity' => ++$entry['quantity'],
    );

    $dbAdapter->update('shoppingcarts', $data, array('user_id = ?' => $user_id, 'product_id = ?' => $product_id));
} else {
    /*SELECT MIN(t1.ID + 1) AS nextID
    FROM tablename t1
    LEFT JOIN tablename t2
       ON t1.ID + 1 = t2.ID
    WHERE t2.ID IS NULL*/

    /*$result = $dbAdapter->fetchRow($dbAdapter->select()
                                             ->from(array('s1' => 'shoppingcarts'), array(new Zend_Db_Expr('MIN(s1.id + 1 ) as nextId')))
                                             ->joinLeft(array('s2' => 'shoppingcarts'), 's1.id + 1 = s2.id', array())
                                             ->where('s2.id IS NULL'));*/

    $data = array(
        'user_id' => $user_id,
        'product_id' => $product_id,
        'quantity' => 1,
    );

    $dbAdapter->insert('shoppingcarts', $data);
}

$quantity = $entry = $dbAdapter->fetchRow($dbAdapter->select()->from('shoppingcarts', array('total' => 'SUM(quantity)'))->where('user_id = ?', $user_id));

$nr_products = ($quantity['total']) ? $quantity['total'] : 0;

$row = $dbAdapter->fetchRow($dbAdapter->select('name')->from('products')->where('id = ?', $product_id));
$product_name = $row['name'];


echo json_encode(array('product_name' => $product_name, 'nr_products' => $nr_products));