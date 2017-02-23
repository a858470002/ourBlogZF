<?php

class Application_Model_Index
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
        $this->setDbTable('Application_Model_DbTable_Types');
        $types = $this->_dbTable->fetchAll()->toArray();
        $view = array();
        foreach ($types as $value) {
            $view[] = "<a class='nav' href='/index/?id=".$value['id']."'>".$value['name']."</a>";
        }
        return $view;
    }

    public function fetchAll($id)
    {
        if(isset($id)){
            $id = filter_var($id,FILTER_VALIDATE_INT,array('options' => array('min_range' => 0)));
            if (!$id){
                header('Location: /');
                exit;
            }
        }
        $this->setDbTable('Application_Model_DbTable_Article');
        if ($id > 0){
            $result = $this->_dbTable->select()->from('article',array('id','title','link','is_link'))->where("`column` = ".$id)->query()->fetchAll();
        } else {
            $result = $this->_dbTable->select()->from('article',array('id','title','link','is_link'))->query()->fetchAll();
        }
        $view = array();
        foreach ($result as $v){
            $view[$v['id']]['title'] = $v['title'];
            if ($v['is_link'] == 0) {
                $view[$v['id']]['href'] = "/index/content/?id=".$v['id'];
                $view[$v['id']]['link'] = '';
            } else {
                $view[$v['id']]['href'] = $v['link'];
                $view[$v['id']]['link'] = "<sup title='It is a link'>[link]</sup>";
            }
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

