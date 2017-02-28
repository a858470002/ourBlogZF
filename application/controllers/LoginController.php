<?php

class LoginController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()){
            header('Location: /admin');
            exit;
        }
    }

    public function loginAction()
    {
        $email    = $this->getRequest()->getPost('email');
        $password = $this->getRequest()->getPost('password');

        try {
            $adapter  = new OurBlog_AuthAdapter($email,$password);
            $adapter->authenticate();
        } catch (InvalidArgumentException $e) {
            echo "<script>alert('".$e->getMessage()."');window.location.href='/login'</script>";
            exit;
        }
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        header('Location: /admin');
        exit;
    }


}

