<?php
set_include_path('../../library');
// var_dump(get_include_path());die;
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
include __DIR__.'/MyApp_DbUnit_ArrayDataSet.php';
require __DIR__.'/dataset.php';

class mainTest extends PHPUnit_Extensions_Database_TestCase
{   
    public function getConnection()
    {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog_test;charset=utf8', 'root', '123456');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->createDefaultDBConnection($pdo, 'blog_test');
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

    //Delete article

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Illegal operation
     */
    public function testDeleteArticleIllegalArticle()
    {
        $user_id    = 1;
        $article_id = 'aaa';
        deleteArticle (PDOStart(), $user_id, $article_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Delete failed: incorrect user
     */
    public function testDeleteArticleWrongUser()
    {
        $user_id    = 2;
        $article_id = 1;
        deleteArticle (PDOStart(), $user_id, $article_id);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Delete failed: article don't exist
     */
    public function testDeleteArticleWrongArticle()
    {
        $user_id    = 1;
        $article_id = 3;
        deleteArticle (PDOStart(), $user_id, $article_id);
    }

    public function testDeleteArticle()
    {
        $user_id    = 1;
        $article_id = 1;
        deleteArticle (PDOStart(), $user_id, $article_id);
        $this->assertEquals(1,$this->getConnection()->getRowCount('article'));
        $this->assertEquals(0,$this->getConnection()->getRowCount('tag_mid'));
    }
}