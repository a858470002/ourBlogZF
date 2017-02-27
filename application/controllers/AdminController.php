<?php

class AdminController extends Zend_Controller_Action
{
    private $user_id;

    private $model;

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

        $this->model = new Application_Model_Admin();
        $this->_helper->layout->setLayout('admin');
    }

    public function indexAction()
    {
        $this->view->entries = $this->model->fetchAll($this->user_id);
    }

    public function addAction()
    {
        // Page of add article
        $this->view->entries = $this->model->fetchColumn();
    }

    public function doaddAction()
    {
        $post = $this->getRequest()->getPost();
        $this->model->addArticle($post, $this->user_id);
    }

    public function editAction()
    {
        // Page of edit article
    }

    public function doeditAction()
    {

    }


}

