<?php

use PayPal\Api\Sale;

class Application_Model_OrderMapper {
    protected $_dbTable;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Order');
        }
        return $this->_dbTable;
    }

    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Order($row);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function find($id){
        $result = $this->getDbTable()->find($id);

        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $order = new Application_Model_Order($row);

        return $order;
    }

    public function stupdate($transactionId, $apiContext) {
        if($transactionId) {

            $sale = Sale::get($transactionId, $apiContext);

            $data = array(
                'state' => $sale->getState(),
            );

            $this->getDbTable()->update($data, array('transaction_id = ?' => $transactionId));
            return $data['state'];
        }
    }
}