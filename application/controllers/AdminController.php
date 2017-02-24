<?php

class AdminController extends Zend_Controller_Action
{
    private $user_id;

    private $_index;

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

    public function addAction()
    {
        // Page of add article
        $this->view->entries = $this->_index->fetchColumn();
    }

    public function doaddAction()
    {
        $post = $this->getRequest()->getPost();
        $this->_index->addArticle($post, $this->user_id);
    }

    public function editAction()
    {
        // Page of edit article
    }

    public function doeditAction()
    {

    }


}

