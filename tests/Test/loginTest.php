<?php
class Test_logintest extends PHPUnit_Extensions_Database_TestCase
{
    public function getConnection()
    {
        global $db;
        return $this->createDefaultDBConnection($db->getConnection(), 'blog_zftest');
    }

    public function getDataSet()
    {
        $arrSet = array(
            'user'=>array(
                array(
                    'id'         => 1,
                    'email'      => 'tianyi@163.com',
                    'password'   => md5('Zfxv' . md5('123'))
                ),
                array(
                    'id'         => 2,
                    'email'      => "'or''@163.com",
                    'password'   => md5('Zfxv' . md5('123'))
                ),
                array(
                    'id'         => 3,
                    'email'      => 'abc@163.com',
                    'password'   => md5('Zfxv' . md5("'or''"))
                )
            )
        );
        return new MyApp_DbUnit_ArrayDataSet($arrSet);
    }
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
        $this->assertEquals(false, $user_id);
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
