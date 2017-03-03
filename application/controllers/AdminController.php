<?php

class AdminController extends Zend_Controller_Action
{
    private $userId;

    private $model;

    public function init()
    {
        // logincheck
        $auth = Zend_Auth::getInstance();
        if (!($auth->hasIdentity())) {
            header('Location: /login');
            exit;
        }
        $this->userId = $auth->getIdentity();
        $this->model  = new Application_Model_Admin();
        $this->_helper->layout->setLayout('admin');
    }

    public function indexAction()
    {
        try {
            $this->view->entries = $this->model->fetchAll($this->userId);
        } catch (Exception $e) {
            echo "<script>alert('Server error!');window.location.href='/'</script>";
            exit;
        }
    }

    public function addAction()
    {
        try {
            $this->view->columns = $this->model->fetchColumns();
        } catch (Exception $e) {
            echo "<script>alert('Server error!');window.location.href='/admin'</script>";
            exit;
        }
    }

    public function doaddAction()
    {
        try {
            $this->model->addArticle($_POST, $this->userId);
        } catch (InvalidArgumentException $e) {
            echo "<script>alert('参数错误');window.location.href='/admin/add'</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Server error!');window.location.href='/admin/add'</script>";
            exit;
        }
        echo "<script>alert('添加成功');window.location.href='/admin';</script>";
        exit;
    }

    public function editAction()
    {
        try {
            $this->view->columns = $this->model->fetchColumns();
            $this->view->article = $this->model->fetchEditArticle($this->userId, $this->getRequest()->getQuery('id'));
        } catch (InvalidArgumentException $e) {
            echo "<script>alert('参数错误');window.location.href='/admin'</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Server error!');window.location.href='/admin'</script>";
            exit;
        }
    }

    public function doeditAction()
    {
        $articleId = $this->getRequest()->getQuery('id');
        try {
            $this->model->editArticle($_POST, $this->userId, $articleId);
        } catch (InvalidArgumentException $e) {
            echo "<script>alert('参数错误');window.location.href='/admin/edit/?id=" . $articleId . "'</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Server error!');window.location.href='/admin/edit/?id=" . $articleId . "'</script>";
            exit;
        }
        echo "<script>alert('修改成功');window.location.href='/admin';</script>";
        exit;
    }
}
