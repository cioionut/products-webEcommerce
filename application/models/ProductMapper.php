<?php

class Application_Model_ProductMapper
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
            $this->setDbTable('Application_Model_DbTable_Product');
        }
        return $this->_dbTable;
    }
 
    public function save(Application_Model_Product $product)
    {
        $data = array(
            'id'          => $product->id,
            'name'        => $product->name,
            'category_id' => $product->getCategoryId(),
            'price'       => $product->price,
            'file'        => $product->file,
            'image'       => $product->image,
            'description' => $product->description,
        );
 
        if (null === ($id = $product->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $product = new Application_Model_Product();
        $product->setId($row->id);
        $product->setName($row->name);
        $product->setCategoryId($row->category_id);
        $product->setFile($row->file);
        $product->setImage($row->image);
        $product->setDescription($row->description);
        $product->setCurrency($row->currency_id, $row->price);
        return $product;
    }
 
    public function fetchAll($userCurrencyId = null, $categoryId = null)
    {
        $resultSet = ($categoryId) ? $this->getDbTable()->fetchAll($this->getDbTable()->select()->where('category_id = ?', $categoryId)) : $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Product($row, $userCurrencyId);

            /*$entry->setId($row->id);
            $entry->setName($row->name);
            $entry->setPrice($row->price);

            $db_adapter = $this->getDbTable()->getAdapter();
            $db = Zend_Db::factory('Mysqli',$db_adapter->getConfig());
            $category = $db->fetchRow($db->select('name')->from('categories')->where('id = ?', $row->category_id));

            $entry->setCategoryId($row->category_id);
            $entry->setFile($row->file);
            $entry->setImage($row->image);
            $entry->setDescription($row->description);*/

            $entries[] = $entry;
        }
        return $entries;
    }

    public function getProductById($id, $userCurrencyId = null){
        //$row = $this->getDbTable()->find($id);
        //$row = $row[0];
        $row = $this->getDbTable()->fetchRow($this->getDbTable()->select()->where('id = ?', $id));
        if (0 == count($row)) {
            return;
        }
        $product = new Application_Model_Product($row, $userCurrencyId);

        return $product;
    }

    public function delete($id){
        $row = $this->getDbTable()->fetchRow($this->getDbTable()->select()->where('id = ?', $id));
        $file = $row->file;
        $image = $row->image;
        if($file) {
            $file = UPLOADS_DATA . '/' .$file;
            if(file_exists($file)){
                unlink($file);
            }
        }
        if($image) {
            $image = UPLOADS_IMAGES . '/' . $image;
            if(file_exists($image)){
                unlink($image);
            }
        }
        $this->getDbTable()->delete("id = $id");
    }

    public function delete_file($file){
        if($file) {
            $file = UPLOADS_DATA . '/' .$file;
            if(file_exists($file)){
                unlink($file);
            }
        }
    }

    public function delete_image($image){
        if($image) {
            $image = UPLOADS_IMAGES . '/' . $image;
            if(file_exists($image)){
                unlink($image);
            }
        }
    }

}

