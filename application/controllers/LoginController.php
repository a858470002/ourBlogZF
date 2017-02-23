<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()){
            header('Location: /');
            exit;
        }
    }

    public function loginAction()
    {
        $email    = $this->getRequest()->getPost('email');
        $password = $this->getRequest()->getPost('password');

        try {
            $adapter  = new OurBlog_AuthAdapter($email,$password);
            $user_id  = $adapter->authenticate();
        } catch (InvalidArgumentException $e) {
            echo "<script>alert('".$e->getMessage()."');window.location.href='/login'</script>";
            exit;
        }
        header('Location: /admin');
        exit;
    }


}

