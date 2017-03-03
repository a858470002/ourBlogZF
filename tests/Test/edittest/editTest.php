<?php
class EditTest extends PHPUnit_Extensions_Database_TestCase
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
                    ),
                array(
                    'id'         => 2,
                    'title'      => 'test article2',
                    'formaltext' => '',
                    'column'     => 1,
                    'user_id'    => 1,
                    'link'       => 'http://www.baidu.com',
                    'is_link'    => 1
                    )
            ),
            'tag'=>array(
                array('id'=>1, 'name'=>'php'),
                array('id'=>2, 'name'=>'java')
            ),
            'tag_mid'=>array(
                array('id'=>1, 'tag_id'=>1, 'article_id'=>1),
                array('id'=>2, 'tag_id'=>2, 'article_id'=>1),
                array('id'=>3, 'tag_id'=>2, 'article_id'=>2)
            )
        );
        return new MyApp_DbUnit_ArrayDataSet($arrSet);
    }

    // FetchEditArticle test
    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Article don\'t exists or illegal user !
     */
    public function testFetchEditArticleDontExist()
    {
        $actualData   = $this->model->fetchEditArticle(1, 3);
    }
    
    public function testFetchEditArticle()
    {
        $actualData   = $this->model->fetchEditArticle(1, 1);
        $expectedData = array(
            'id'         => '1',
            'title'      => 'test article',
            'formaltext' => 'wojiushi zhengwen',
            'column'     => '1',
            'user_id'    => '1',
            'link'       => '',
            'is_link'    => '0',
            'tags'        => 'php,java'
        );
        $this->assertEquals($expectedData, $actualData);
    }

    // EditArticle test

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key title
     */
    public function testEditArticleNullTitle()
    {
        unset($this->data['title']);
        $this->model->editArticle($this->data, 1, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Please fill the title
     */
    public function testEditArticleEmptyTitle()
    {
        $this->data['title'] = '';
        $this->model->editArticle($this->data, 1, 1);
    }
    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Title is over range(255)!
     */
    public function testEditArticleTooLongTitle()
    {
        $this->data['title'] = str_pad('i', 256, 'i');
        $this->model->editArticle($this->data, 1, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key formaltext
     */
    public function testEditArticleNullFormaltext()
    {
        unset($this->data['formaltext']);
        $this->model->editArticle($this->data, 1, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage The formaltext can not be empty
     */
    public function testEditArticleEmptyFormaltext()
    {
        $this->data['formaltext'] = '';
        $this->model->editArticle($this->data, 1, 1);
    }
    
    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Formaltext is over range(65535)!
     */
    public function testEditArticleTooLongFormaltext()
    {
        $this->data['formaltext'] = str_pad('i', 65536, 'i');
        $this->model->editArticle($this->data, 1, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key link
     */
    public function testEditArticleNullLink()
    {
        $this->data['formaltext'] = '';
        unset($this->data['link']);
        $this->model->editArticle($this->data, 1, 2);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage The link can not be empty
     */
    public function testEditArticleEmptyLink()
    {
        $this->data['formaltext'] = '';
        $this->model->editArticle($this->data, 1, 2);
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
    public function testEditArticleLink($link)
    {
        $this->data['formaltext'] = '';
        $this->data['link'] = $link;
        $this->model->editArticle($this->data, 1, 2);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Link is over range(2000)!
     */
    public function testEditArticleTooLongLink()
    {
        $this->data['formaltext'] = '';
        $this->data['link'] = 'http://w.' . str_pad('i', 1992, 'i');
        $this->model->editArticle($this->data, 1, 2);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Missing requied key column
     */

    public function testEditArticleNullColumn()
    {
        unset($this->data['column']);
        $this->model->editArticle($this->data, 1, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Column is invalid
     */
    public function testEditArticleIllegalColumn()
    {
        $this->data['column'] = 1.2;
        $this->model->editArticle($this->data, 1, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Don't use over 10 tags
     */
    public function testEditArticleTenMoreTags()
    {
        $this->data['tag'] = 'php,java,js,php,java,js,php,java,js,php,java,js';
        $this->model->editArticle($this->data, 1, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Some of tags is over range(32)!
     */
    public function testEditArticleToolongTags()
    {
        $this->data['tag'] = 'php,java,aaaaabbbbbcccccdddddeeeeefffffggg';
        $this->model->editArticle($this->data, 1, 1);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage It's not your article
     */
    public function testEditArticleWrongUser()
    {
        $this->model->editArticle($this->data, 2, 1);
    }

    public function testEditArticleAddTag()
    {
        $this->data['tag'] = 'php,java,js';
        $this->model->editArticle($this->data, 1, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-EditArticleAddTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article', 'tag', 'tag_mid'));

        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }

    public function testEditArticleReduceTag()
    {
        $this->data['tag'] = 'php,';
        $this->model->editArticle($this->data, 1, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-EditArticleReduceTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article', 'tag', 'tag_mid'));

        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }

    public function testEditArticleEmptyTag()
    {
        $this->data['tag'] = '';
        $this->model->editArticle($this->data, 1, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-EditArticleEmptyTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article', 'tag', 'tag_mid'));

        $this->assertEquals(1, $this->getConnection()->getRowCount('tag_mid'));
        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }
    
    public function testEditArticleAllNewTag()
    {
        $this->data['tag'] = 'linux,swift';
        $this->model->editArticle($this->data, 1, 1);
        $expectedTable = new MyApp_DbUnit_ArrayDataSet(include __DIR__ . '/except-EditArticleAllNewTag.php');
        $actualTable   = $this->getConnection()->createDataSet(array('article', 'tag', 'tag_mid'));

        $this->assertDataSetsEqual($expectedTable, $actualTable);
    }

    public function testFetchAll()
    {
        $actualData   = $this->model->fetchAll(1);
        $expectedData = array(
            '1' =>array(
                'title' => 'test article',
                'edit'  => '/admin/edit/?id=1',
                'del'   => '/admin/del/?id=1'
            ),
            '2' =>array(
                'title' => 'test article2',
                'edit'  => '/admin/edit/?id=2',
                'del'   => '/admin/del/?id=2'
            )
        );
        $this->assertEquals($expectedData, $actualData);
    }
}
