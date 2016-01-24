<?php

class Application_Model_MailsettingMapper
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
            $this->setDbTable('Application_Model_DbTable_Mailsetting');
        }
        return $this->_dbTable;
    }

    public function getDefault(){
        $result = $this->getDbTable()->fetchRow($this->getDbTable()->select('id')->where('default_config = ?', 1));
        if(count($result) == 0 ) return 0;
        else return $result['id'];
    }

    public function setDefault($id){
        $default_id = $this->getDefault();
        $result1 = $this->getDbTable()->update(array('default_config' => 0), array('id = ?' => $default_id));
        $result2 = $this->getDbTable()->update(array('default_config' => 1), array('id = ?' => $id));
        return $result1 && $result2;
    }

    public function update($id,$json){
        $result = $this->getDbTable()->update(array('smtp_config' => $json), array('id = ?' => $id));
        return $result;
    }

    public function delete($id){
        $result = $this->getDbTable()->delete(array('id = ?' => $id));;
        return $result;
    }




    public function getConfig($id){
        $result = $this->getDbTable()->fetchRow($this->getDbTable()->select('smtp_config')->where('id = ?',$id));
        if(count($result) == 0 ) throw new Exception('Mail configuration missing');
        $cfg = json_decode($result->smtp_config, true);
        $mailSetting = new Application_Model_Mailsetting($cfg);
        return $mailSetting;
    }

    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $cfg = json_decode($row->smtp_config, true);
            $entry = new Application_Model_Mailsetting($cfg);
            $entry->setId($row->id);
            $entry->setDefaultConfig($row->default_config);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function save(Application_Model_Mailsetting $setting)
    {
        $data = array(
            'id'          => $setting->id,
            'smtp_config' => $setting->getJsonConfig(),
        );

        if (null === ($id = $setting->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

}
 