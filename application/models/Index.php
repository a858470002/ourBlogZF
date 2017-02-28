<?php

class Application_Model_Index
{
    protected $dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->dbTable = $dbTable;
        return $this->dbTable;
    }

 
    public function fetchType()
    {
        $result = $this->setDbTable('Application_Model_DbTable_Types')->fetchAll();
        $view = array();
        foreach ($result as $value) {
            $view[] = "<a class='nav' href='/index/?id=".$value['id']."'>".$value['name']."</a>";
        }
        return $view;
    }

    public function fetchAll($id)
    {
        if (isset($id)) {
            $id = filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0)));
            if (!$id) {
                header('Location: /');
                exit;
            }
        }
        $article = $this->setDbTable('Application_Model_DbTable_Article');
        if ($id > 0) {
            $result = $article->select()->from('article', array('id', 'title', 'link', 'is_link'))->where("`column` = ".$id)->query()->fetchAll();
        } else {
            $result = $article->select()->from('article', array('id', 'title', 'link', 'is_link'))->query()->fetchAll();
        }
        $view = array();
        foreach ($result as $v) {
            if ($v['is_link'] == 0) {
                $view[$v['id']] = array(
                    'title'=> $v['title'],
                    'href' => "/index/content/?id=".$v['id'],
                    'link' => ''
                    );
            } else {
                $view[$v['id']] = array(
                    'title'=> $v['title'],
                    'href' => $v['link'],
                    'link' => "<sup title='It is a link'>[link]</sup>"
                    );
            }
        }
        return $view;
    }

    public function content($id)
    {
        if (isset($id)) {
            $id = filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
            if (!$id) {
                header('Location: /');
                exit;
            }
        }
        $article = $this->setDbTable('Application_Model_DbTable_Article');
        $result  = $article->find($id);
        return $result->current();
    }
}
