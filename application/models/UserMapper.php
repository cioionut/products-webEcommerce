<?php

class Application_Model_UserMapper
{
    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_User');
        }
        return $this->_dbTable;
    }

    public function insert(Application_Model_User $user){
        $data = array(
            'email'         => $user->email,
            'password'      => $user->password,
            'currency_id'   => $user->getCurrencyId(),
            'hash'          => $user->hash,
        );
        return $this->getDbTable()->insert($data);
    }

    public function verify($email, $hash)
    {

        $result = $this->getDbTable()->fetchRow($this->getDbTable()->select('id')
                                                                    ->where('email = ?', $email)
                                                                    ->where('hash = ?', $hash)
                                                                    ->where('verified =?', 0));
        if(count($result) == 1){
            if($this->getDbTable()->update(
                    array(
                        'verified' => 1
                    ),
                    array(
                        'email = ?' => $email
                    )
                )
            ) return true;
        }
        return false;
    }

    public function delete(Application_Model_User $user){
        return $this->getDbTable()->delete(array('email = ?' => $user->email));
    }

    public function fetchAll()
    {
        //$resultSet = $this->getDbTable()->fetchAll();
        //$row = $this->getDbTable()->fetchRow($this->getDbTable()->select()->where('id = ?', 4));
        $resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()
                                                                      ->from(array('u' => 'users'),
                                                                          array('id', 'email', 'verified'))
                                                                      ->joinLeft(array('a' => 'admins'),
                                                                          'u.id = a.user_id',
                                                                          array('admin_id' => 'a.id'))
                                                                      ->setIntegrityCheck(false));
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_User($row);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function getUserByEmail($email){
        $result = $this->getDbTable()->fetchRow($this->getDbTable()->select()->where('email = ?', $email));
        if (0 == count($result)) {
            return;
        }
        $user = new Application_Model_User($result);
        return $user;
    }

    public function getShoppingCart($user) {

        $results = $this->getDbTable()->fetchAll($this->getDbTable()->select()
                                                         ->from(array('p' => 'products'),
                                                             array('id', 'name', 'price'))
                                                         ->join(array('c' => 'categories'),
                                                             'c.id = p.category_id',
                                                             array('category' => 'c.name', 'category_id' => 'c.id'))
                                                         ->join(array('s' => 'shoppingcarts'),
                                                             's.product_id = p.id',
                                                             array('quantity' => 's.quantity'))
                                                         ->where('s.user_id = ?', $user->id)
                                                         ->setIntegrityCheck(false));
        $cartItems = array();
        foreach ($results as $result) {
            $cartItems[] = new Application_Model_CartItem($result, $user->currency_id);
        }
        return $cartItems;
    }



    public function umkAdmin($admin_id){
        $db_adapter = $this->getDbTable()->getAdapter();
        $db = Zend_Db::factory('Mysqli',$db_adapter->getConfig());
        $db->delete('admins', array('user_id = ?' => $admin_id));
    }

    public function mkAdmin($user_id){
        $db_adapter = $this->getDbTable()->getAdapter();
        $db = Zend_Db::factory('Mysqli',$db_adapter->getConfig());
        $db->insert('admins', array('user_id' => $user_id));
    }

    public function find($id) {
        $result = $this->getDbTable()->find($id);

        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $category = new Application_Model_User($row);

        return $category;
    }
}