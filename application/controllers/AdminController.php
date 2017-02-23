<?php

class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_index = new Application_Model_Admin();
        $this->view->types = $this->_index->fetchType();
    }

    public function indexAction()
    {
        // action body
        $this->view->entries = $this->_index->fetchAll();
        // var_dump($this->view->entries);
        // exit;
    }


}

