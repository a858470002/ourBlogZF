<?php

class IndexController extends Zend_Controller_Action
{
    private $model;
    
    public function init()
    {
        /* Initialize action controller here */
        $this->model = new Application_Model_Index();
        $this->view->types = $this->model->fetchType();
    }

    public function indexAction()
    {
        $id = $this->getRequest()->getQuery('id');
        $this->view->entries = $this->model->fetchAll($id);
    }

    public function contentAction()
    {
        $id = $this->getRequest()->getQuery('id');
        $this->view->content = $this->model->content($id);
    }
}
