<?php

class Application_Model_Article
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
            $this->setDbTable('Application_Model_DbTable_Article');
        }
        return $this->_dbTable;
    }
 
    public function showArticle()
    {

    }

    public function factory()
    {
        $db = Zend_Db::factory('Pdo_Mysql', array(
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'dbname'   => 'blog_zf',
            'charset'  => 'utf8'
        ));

        $select = $db->select()->from('article');
        var_dump($select);
        exit;
    }

}

