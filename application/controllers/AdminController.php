<?php

class AdminController extends Zend_Controller_Action
{
    private $user_id;

    public function init()
    {
        // logincheck
        $auth = Zend_Auth::getInstance();
        if (!($auth->hasIdentity())){
            header('Location: /login');
            exit;
        }
        $user_id = $auth->getIdentity();
        $this->user_id = $user_id;

        $this->_index = new Application_Model_Admin();
        $this->_helper->layout->setLayout('admin');
    }

    public function indexAction()
    {
        $this->view->entries = $this->_index->fetchAll($this->user_id);
    }


}

