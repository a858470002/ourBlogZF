<?php

class Application_Model_Admin
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

    public function fetchAll($user_id)
    {
        $this->setDbTable('Application_Model_DbTable_Article');
        $result = $this->_dbTable->select()->from('article',array('id','title'))->where("user_id = ".$user_id)->query()->fetchAll();

        $view = array();
        foreach ($result as $v){
            $view[$v['id']]['title'] = $v['title'];
            $view[$v['id']]['edit']  = "/admin/edit/?id=".$v['id'];
            $view[$v['id']]['del']   = "/admin/del/?id=".$v['id'];
        }
        return $view;
    }

}

