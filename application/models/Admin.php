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

 
    public function fetchType()
    {
        $view = array(
            "<a class='nav' href='/admin'>博文管理</a>",
            "<a class='nav' href='/admin/add'>写博文</a>"
            );
        return $view;
    }

    public function fetchAll()
    {
        $this->setDbTable('Application_Model_DbTable_Article');
        $result = $this->_dbTable->select()->from('article',array('id','title'))->query()->fetchAll();

        $view = array();
        foreach ($result as $v){
            $view[$v['id']]['title'] = $v['title'];
            $view[$v['id']]['edit']  = "/admin/edit/?id=".$v['id'];
            $view[$v['id']]['del']   = "/admin/del/?id=".$v['id'];
        }
        return $view;
    }

    public function content($id)
    {
        if(isset($id)){
            $id = filter_var($id,FILTER_VALIDATE_INT,array('options' => array('min_range' => 1)));
            if (!$id){
                header('Location: /');
                exit;
            }
        }
        $this->setDbTable('Application_Model_DbTable_Article');
        $result = $this->_dbTable->find($id);
        return $result->current()->toArray();
    }

}

