<?php
set_include_path('../../library');
require_once '../Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('OurBlog_');
$db = new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'dbname'   => 'blog_zftest',
            'charset'  => 'utf8'
        ));
Zend_Db_Table_Abstract::setDefaultAdapter($db);
require_once '../../application/models/DbTable/User.php';

class Test_logintest extends PHPUnit_Framework_TestCase
{   
    // Login test


    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Please fill the Email
     */
    public function testLoginEmptyEmail()
    {
        $data = array('email'=>'', 'password'=>'');
        $adapter  = new OurBlog_AuthAdapter($data['email'], $data['password']);
        $adapter->authenticate();
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Illegal Email address
     */
    public function testLoginIllegalEmail()
    {
        $data = array('email'=>'tianyi@163', 'password'=>'!@#$%^^&*(');
        $adapter  = new OurBlog_AuthAdapter($data['email'], $data['password']);
        $adapter->authenticate();
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Please fill the Password
     */
    public function testLoginEmptyPassword()
    {
        $data = array('email'=>'aaa@163.com', 'password'=>'');
        $adapter  = new OurBlog_AuthAdapter($data['email'], $data['password']);
        $adapter->authenticate();
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Incorrect email or password
     */
    public function testLoginIncorrectEmail()
    {
        $data = array('email'=>'aaaaa@163.com', 'password'=>'123');
        $adapter  = new OurBlog_AuthAdapter($data['email'], $data['password']);
        $user_id  = $adapter->authenticate();
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Incorrect email or password
     */
    public function testLoginIncorrectPassword()
    {
        $data = array('email'=>'tianyi@163.com', 'password'=>'000000');
        $adapter  = new OurBlog_AuthAdapter($data['email'], $data['password']);
        $user_id  = $adapter->authenticate();
        $this->assertEquals(False, $user_id);
    }

    public function testLoginEmailInject()
    {
        $data = array('email'=>"'or''@163.com", 'password'=>'123');
        $adapter  = new OurBlog_AuthAdapter($data['email'], $data['password']);
        $actualResult =  $adapter->authenticate();
        $expectedResult = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, 2);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testLoginPasswordInject()
    {
        $data = array('email'=>'abc@163.com', 'password'=>"'or''");
        $adapter  = new OurBlog_AuthAdapter($data['email'], $data['password']);
        $actualResult =  $adapter->authenticate();
        $expectedResult = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, 3);
        $this->assertEquals($expectedResult, $actualResult);
    }
}