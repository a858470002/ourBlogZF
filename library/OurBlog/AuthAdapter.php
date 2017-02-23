<?php 
class OurBlog_AuthAdapter implements Zend_Auth_Adapter_Interface
{
    /**
     * Sets email and password for authentication
     *
     * @return void
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
 
    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot
     *                                     be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        // Email
        $email = $this->email;
        if (empty($email)){
            throw new InvalidArgumentException("Please fill the Email");
        }
        $email = filter_var(($email), FILTER_VALIDATE_EMAIL);
        if (!$email){
            throw new InvalidArgumentException("Illegal Email address");
        }

        // Password
        $password = $this->password;
        if (empty($password)){
            throw new InvalidArgumentException("Please fill the Password");
        }
        $password = md5("Zfxv".md5($password));

        $user = new Application_Model_DbTable_User;
        $result = $user->select()
                       ->from('user','id')
                       ->where('email = ?', $email)
                       ->where('password = ?', $password)
                       ->query()->fetchAll();
        if (empty($result)){
            throw new InvalidArgumentException("Incorrect email or password");
        }

        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $result[0]['id']);
    }
}