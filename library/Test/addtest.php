<?php
set_include_path('../../library');
require_once '../Zend/Loader/Autoloader.php';
require_once '../../application/models/Admin.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('OurBlog_');
// $db = new Zend_Db_Adapter_Pdo_Mysql(array(
//             'host'     => '127.0.0.1',
//             'username' => 'root',
//             'password' => '123456',
//             'dbname'   => 'blog_zftest',
//             'charset'  => 'utf8'
//         ));
// Zend_Db_Table_Abstract::setDefaultAdapter($db);
include __DIR__.'/MyApp_DbUnit_ArrayDataSet.php';
require __DIR__.'/dataset.php';

class mainTest extends PHPUnit_Extensions_Database_TestCase
{   
    public function getConnection()
    {
        $db = new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'dbname'   => 'blog_zftest',
            'charset'  => 'utf8'
        ));
        return $this->createDefaultDBConnection($db->getConnection(), 'blog_test');
    }

    public function getDataSet()
    {
        $ArrSet = array(
            'user'=>array(
                array(
                    'id'         => 1,
                    'email'      => 'tianyi@163.com',
                    'password'   => md5('123456')
                    ),
                array(
                    'id'         => 2,
                    'email'      => "'or''@163.com",
                    'password'   => md5('123456')
                    ),
                array(
                    'id'         => 3,
                    'email'      => 'abc@163.com',
                    'password'   => md5("'or''")
                    )
            ),
            'article'=>array(
                array(
                    'id'         => 1,
                    'title'      => 'test article',
                    'formaltext' => 'wojiushi zhengwen',
                    'column'     => 1,
                    'user_id'    => 1,
                    'link'       => '',
                    'is_link'    => 0
                    ),
                array(
                    'id'         => 2,
                    'title'      => 'test article2',
                    'formaltext' => '',
                    'column'     => 1,
                    'user_id'    => 1,
                    'link'       => 'http://www/baidu.com',
                    'is_link'    => 1
                    )
            ),
            'tag'=>array(
                array('id'=>1, 'name'=>'php', 'user_id'=>1),
                array('id'=>2, 'name'=>'java', 'user_id'=>1)
            ),
            'tag_mid'=>array(
                array('id'=>1, 'tag_id'=>1, 'article_id'=>1),
                array('id'=>2, 'tag_id'=>2, 'article_id'=>1)
            )
        );
        return new MyApp_DbUnit_ArrayDataSet($ArrSet);
    }

    // Add article test

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key column
     */
    public function testAddArticleUnsetColumn()
    {
        $data   = array(
            'title'      => 'title',
            'formaltext' => 'testFormaltext',
            'tag'        => 'java,php',
            'link'       => ''
            );
        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Column is invalid
     */
    public function testAddArticleEmptyColumn()
    {
        $data = array(
            'title'      => 'testTitle',
            'formaltext' => 'testFormaltext',
            'column'     => '',
            'tag'        => 'java,php',
            'link'       => ''
            );

        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key title
     */
    public function testAddArticleUnsetTitle()
    {
        $data   = array(
            'formaltext' => 'testFormaltext',
            'column'     => 1,
            'tag'        => 'java,php',
            'link'       => ''
            );
        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Please fill the title
     */
    public function testAddArticleEmptyTitle()
    {
        $data   = array(
            'title'      => '',
            'formaltext' => 'testFormaltext',
            'column'     => 1,
            'tag'        => 'java,php',
            'link'       => ''
            );
        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Title is over range(64)!
     */
    public function testAddArticleOverRangeTitle()
    {
        $data = array(
            'title'      => '1234567890123456789012345678901234567890123456789012345678901234567890',
            'formaltext' => 'testFormaltext',
            'column'     => 1,
            'tag'        => 'java,php',
            'link'       => ''
            ); 
        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key formaltext
     */
    public function testAddArticleUnsetFormaltext()
    {
        $data   = array(
            'title'      => 'title',
            'column'     => 1,
            'tag'        => 'java,php',
            'link'       => ''
            );
        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage You should fill content
     */
    public function testAddArticleEmptyFormaltext()
    {
        $data = array(
            'title'      => 'title',
            'formaltext' => '',
            'column'     => 1,
            'tag'        => 'java,php',
            'link'       => ''
            );
        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage You should fill content
     */
    public function testAddArticleEmptyLink()
    {
        $data = array(
            'title'      => 'title',
            'formaltext' => '',
            'column'     => 1,
            'tag'        => 'java,php',
            'link'       => ''
            );
        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage One of params(formaltext, link) must be empty
     */
    public function testAddArticleSetBothFormaltextAndLink()
    {
        $data = array(
            'title'      => 'title',
            'formaltext' => 'formaltext',
            'column'     => 1,
            'tag'        => 'java,php',
            'link'       => 'http;//www.baidu.com'
            );
        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }
    
    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Don't use over 10 tags
     */
    public function testAddArticle10MoreTags()
    {
        $data = array(
            'title'      => 'testTitle',
            'formaltext' => 'testFormaltext',
            'column'     => 1,
            'tag'        => 'java,php,php3,php4,php5,php6,php7,php8,php9,php10,php11',
            'link'       => ''
            );

        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Some of tags is over range(32)!
     */
    public function testAddArticleOverRangeTags()
    {
        $data = array(
            'title'      => 'testTitle',
            'formaltext' => 'testFormaltext',
            'column'     => 1,
            'tag'        => 'java,php,php4567890123456789012345678901234',
            'link'       => ''
            );

        $user_id = 1;
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
    }

    public function testAddArticleTitleInjection()
    {
        $data = array(
            'title'      => "testTitle'or''", 
            'formaltext' => 'testFormaltext', 
            'column'     => 1, 
            'tag'        => '',
            'link'       => ''
        );
        $user_id = 1;
        $result  = arrset();
        $result['article'][2]['title'] = "testTitle'or''";
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet($result);
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable,$actualTable);
    }

    public function testAddArticleSQLinjection()
    {
        $data = array(
            'title'      => 'testTitle', 
            'formaltext' => "testFormaltext'or''", 
            'column'     => 1, 
            'tag'        => '',
            'link'       => ''
            );
        $user_id = 1;
        $result  = arrset();
        $result['article'][2]['formaltext'] = "testFormaltext'or''";
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet($result);
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable,$actualTable);
    }

    public function testAddArticleWithTag()
    {
        $data = array(
            'title'      => 'testTitle', 
            'formaltext' => 'testFormaltext', 
            'column'     => 1, 
            'tag'        => 'java',
            'link'       => ''
            );
        $user_id = 1;
        $result  = arrset();
        $result['tag_mid'][2] = array('id'=>3, 'tag_id'=>2, 'article_id'=>3);
        $admin = new Application_Model_Admin;
        $admin->addArticle($data, $user_id);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet($result);
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable,$actualTable);
    }

}