<?php

class IndexController extends Zend_Controller_Action
{
    private $_index;
    
    public function init()
    {
        /* Initialize action controller here */
        $this->_index = new Application_Model_Index();
        $this->view->types = $this->_index->fetchType();
    }

    public function indexAction()
    {
        $id = $this->getRequest()->getQuery('id');
        $this->view->entries = $this->_index->fetchAll($id);
    }

    public function contentAction()
    {
        $id = $this->getRequest()->getQuery('id');
        $this->view->content = $this->_index->content($id);
    }


}

