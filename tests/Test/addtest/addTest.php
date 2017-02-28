<?php
class mainTest extends PHPUnit_Extensions_Database_TestCase
{   
    private $data;

    public function getConnection()
    {
        global $db;
        return $this->createDefaultDBConnection($db->getConnection(), 'blog_zftest');
    }

    public function getDataSet()
    {
        $this->data = array(
            'title'      => 'title',
            'formaltext' => 'testFormaltext',
            'column'     => 1,
            'tag'        => 'java,php',
            'link'       => ''
            );
        $ArrSet = array(
            'article'=>array(
                array(
                    'id'         => 1,
                    'title'      => 'test article',
                    'formaltext' => 'wojiushi zhengwen',
                    'column'     => 1,
                    'user_id'    => 1,
                    'link'       => '',
                    'is_link'    => 0
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
        unset($this->data['column']);
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Column is invalid
     */
    public function testAddArticleEmptyColumn()
    {
        $this->data['column'] = '';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key title
     */
    public function testAddArticleUnsetTitle()
    {
        unset($this->data['title']);
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Please fill the title
     */
    public function testAddArticleEmptyTitle()
    {
        $this->data['title'] = '';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Title is over range(64)!
     */
    public function testAddArticleOverRangeTitle()
    {
        $this->data['title'] = '
1234567890123456789012345678901234567890123456789012345678901234567890';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key formaltext
     */
    public function testAddArticleUnsetFormaltext()
    {
        unset($this->data['formaltext']);
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage You should fill content
     */
    public function testAddArticleEmptyFormaltext()
    {
        $this->data['formaltext'] = '';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }
    
    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Don't use over 10 tags
     */
    public function testAddArticle10MoreTags()
    {
        $this->data['tag'] = 'java,php,php3,php4,php5,php6,php7,php8,php9,php10,php11';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Some of tags is over range(32)!
     */
    public function testAddArticleOverRangeTags()
    {
        $this->data['tag'] = 'java,php,php4567890123456789012345678901234';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
    }

    public function testAddArticleSQLinjection()
    {
        $this->data['title'] = "testTitle'or''";
        $this->data['formaltext'] = "testFormaltext'or''";
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-SQLInjection.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable,$actualTable);
    }

    public function testAddArticleWithoutTag()
    {
        $this->data['tag'] = '';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-WithoutTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable,$actualTable);
    }

    public function testAddArticleWithNewTag()
    {
        $this->data['tag'] = 'js,c++';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-WithNewTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable,$actualTable);
    }

    public function testAddArticleWithSomeNewTag()
    {
        $this->data['tag'] = 'php,java,js';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-WithSomeNewTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable,$actualTable);
    }

    public function testAddArticleWithSomeSameTag()
    {
        $this->data['tag'] = 'php,java,js,js,js,php,java';
        $admin = new Application_Model_Admin;
        $admin->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-WithSomeNewTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable,$actualTable);
    }

}