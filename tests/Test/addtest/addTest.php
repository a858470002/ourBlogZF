<?php
class AddTest extends PHPUnit_Extensions_Database_TestCase
{
    private $data;

    private $model;

    public function getConnection()
    {
        global $db;
        $this->model = new Application_Model_Admin();
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
        $arrSet = array(
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
                array('id'=>1, 'name'=>'php'),
                array('id'=>2, 'name'=>'java')
            ),
            'tag_mid'=>array(
                array('id'=>1, 'tag_id'=>1, 'article_id'=>1),
                array('id'=>2, 'tag_id'=>2, 'article_id'=>1)
            )
        );
        return new MyApp_DbUnit_ArrayDataSet($arrSet);
    }

    // Add article test

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key column
     */
    public function testAddArticleUnsetColumn()
    {
        unset($this->data['column']);
        $this->model->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Column is invalid
     */
    public function testAddArticleEmptyColumn()
    {
        $this->data['column'] = '';
        $this->model->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key title
     */
    public function testAddArticleUnsetTitle()
    {
        unset($this->data['title']);
        $this->model->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Please fill the title
     */
    public function testAddArticleEmptyTitle()
    {
        $this->data['title'] = '';
        $this->model->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Title is over range(255)!
     */
    public function testAddArticleOverRangeTitle()
    {
        $this->data['title'] = str_pad('i', 256, 'i');
        $this->model->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key formaltext
     */
    public function testAddArticleUnsetFormaltext()
    {
        unset($this->data['formaltext']);
        $this->model->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Formaltext is over range(65535)!
     */
    public function testAddArticleOverRangeFormaltext()
    {
        $this->data['formaltext'] = str_pad('i', 65536, 'i');
        $this->model->addArticle($this->data, 1);
    }
    
    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key link
     */
    public function testAddArticleUnsetlink()
    {
        unset($this->data['link']);
        $this->model->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage You should fill content
     */
    public function testAddArticleEmptyFormaltextAndLink()
    {
        $this->data['formaltext'] = '';
        $this->model->addArticle($this->data, 1);
    }

    public function invalidURLs()
    {
        return array(
            array('wwww.www.'),
            array('http:/wwww.www'),
            array('htt//wwww.www'),
            array('htttttttp'),
            array(123)
        );
    }
    
    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Link is invalid
     * @dataProvider invalidURLs
     */
    public function testAddArticleIvalidLink($link)
    {
        $this->data['formaltext'] = '';
        $this->data['link'] = $link;
        $this->model->addArticle($this->data, 1);
    }
    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Don't use over 10 tags
     */
    public function testAddArticle10MoreTags()
    {
        $this->data['tag'] = 'java,php,php3,php4,php5,php6,php7,php8,php9,php10,php11';
        $this->model->addArticle($this->data, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Some of tags is over range(32)!
     */
    public function testAddArticleOverRangeTags()
    {
        $this->data['tag'] = 'java,php,php4567890123456789012345678901234';
        $this->model->addArticle($this->data, 1);
    }

    public function testAddArticleSQLinjection()
    {
        $this->data['title'] = "testTitle'or''";
        $this->data['formaltext'] = "testFormaltext'or''";
        $this->model->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-SQLInjection.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }

    public function testAddArticleWithoutTag()
    {
        $this->data['formaltext'] = '';
        $this->data['link'] = 'http://baidu.com';
        $this->data['tag'] = '';
        $this->model->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-WithoutTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }

    public function testAddArticleWithNewTag()
    {
        $this->data['tag'] = 'js,c++';
        $this->model->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-WithNewTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }

    public function testAddArticleWithSomeNewTag()
    {
        $this->data['tag'] = 'php,java,js';
        $this->model->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-WithSomeNewTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }

    public function testAddArticleWithSomeStupidTag()
    {
        $this->data['tag'] = 'php,java,js,js,js,java,,  , ';
        $this->model->addArticle($this->data, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-WithSomeNewTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article','tag','tag_mid'));

        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }
}
