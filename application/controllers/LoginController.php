<?php

class LoginController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            header('Location: /admin');
            exit;
        }
    }

    public function loginAction()
    {
        try {
            $auth = Zend_Auth::getInstance();
            $adapter  = new OurBlog_AuthAdapter($this->getRequest()->getPost('email'), $this->getRequest()->getPost('password'));
            $auth->authenticate($adapter);
        } catch (InvalidArgumentException $e) {
            echo "<script>alert('".$e->getMessage()."');window.location.href='/login'</script>";
            exit;
        }
        header('Location: /admin');
        exit;
    }
}
