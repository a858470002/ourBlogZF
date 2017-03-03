<?php

class Application_Model_Admin
{
    private $dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->dbTable = $dbTable;
        return $this->dbTable;
    }

    private function dbConnect()
    {
        return Zend_Db_Table_Abstract::getDefaultAdapter();
    }

    public function fetchAll($userId)
    {
        $article = $this->setDbTable('Application_Model_DbTable_Article');
        $result  = $article->select()
                           ->from('article', array('id','title'))
                           ->where("user_id = " . $userId)
                           ->query()
                           ->fetchAll();

        $view = array();
        foreach ($result as $v) {
            $view[$v['id']] = array(
                'title' => $v['title'],
                'edit'  => "/admin/edit/?id=" . $v['id'],
                'del'   => "/admin/del/?id=" . $v['id']
            );
        }
        return $view;
    }

    // fetch column to add page
    public function fetchColumns()
    {
        $types = $this->setDbTable('Application_Model_DbTable_Types');
        $result = $types->fetchAll();
        return $result;
    }

    public function addArticle($data, $userId)
    {
        $requiredKeys = array('column', 'title', 'formaltext', 'link', 'tag');
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("Missing requied key $key");
            }
        }

        //column
        $column = filter_var($data['column'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$column) {
            throw new InvalidArgumentException('Column is invalid');
        }

        //title
        $title = trim($data['title']);
        if (empty($title)) {
            throw new InvalidArgumentException('Please fill the title');
        }
        $length = mb_strlen($title, 'UTF-8');
        if ($length > 255) {
            throw new InvalidArgumentException('Title is over range(255)!');
        }

        //link or formaltext
        $link = trim($data['link']);
        $formaltext = trim($data['formaltext']);

        if ($formaltext) {
            $length  = strlen($formaltext);
            if ($length > 65534) {
                throw new InvalidArgumentException('Formaltext is over range(65535)!');
            }
            $isLink = 0;
        } else {
            $length  = mb_strlen($link, 'UTF-8');
            if ($length == 0) {
                throw new InvalidArgumentException('You should fill content');
            }
            if ($length > 2000) {
                throw new InvalidArgumentException('Link is over range(2000)!');
            }
            $link = filter_var($link, FILTER_VALIDATE_URL);
            if (!$link) {
                throw new InvalidArgumentException('Link is invalid');
            }
            $isLink = 1;
        }

        $db = $this->dbConnect();
        //tag
        $tags = array();
        if (!empty($data['tag'])) {
            $tagsExplode = explode(',', $data['tag']);
            if (count($tagsExplode) >= 10) {
                throw new InvalidArgumentException("Don't use over 10 tags");
            }
            foreach ($tagsExplode as $tag) {
                if (!in_array($tag, $tags) && trim($tag) != '') {
                    $length = mb_strlen($tag, 'UTF-8');
                    if ($length > 32) {
                        throw new InvalidArgumentException('Some of tags is over range(32)!');
                    }
                    $tags[] = trim($tag);
                }
            }
        }

        // db start
        $db->beginTransaction();

        try {
            $db->insert('article', array(
                    'title'      => $title,
                    'formaltext' => $formaltext,
                    'column'     => $column,
                    'user_id'    => $userId,
                    'link'       => $link,
                    'is_link'    => $isLink
            ));
            $articleId = $db->lastInsertId();

            // If have tags
            if (!empty($tags)) {
                // Select all tags ,match the same
                $sql = "SELECT * FROM tag WHERE name in (?" . str_repeat(',?', count($tags)-1).") ";
                
                $sameTags = $db->fetchAll($sql, $tags);

                $tagsId   = array();
                $tagsName = array();

                //Find the same tags id & name
                foreach ($sameTags as $tag) {
                    $tagsId[]   = $tag["id"];
                    $tagsName[] = $tag["name"];
                }

                //Tags:which is not in table
                $tagsDiff = array_diff($tags, $tagsName);
            } else {
                $tagsDiff = array();
            }

            //If appear new tags
            if (!empty($tagsDiff)) {
                // 3.insert new tag (match, and del the same tag)
                foreach ($tagsDiff as $tag) {
                    $db->insert('tag', array('name' => $tag));
                    $tagsId[] = $db->lastInsertId();
                }
            }

            if (!empty($tags)) {
                // 4.insert new tag & article (table tag_mid)
                foreach ($tagsId as $id) {
                    $db->insert('tag_mid', array(
                        'tag_id'     => $id,
                        'article_id' => $articleId
                    ));
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function fetchEditArticle($userId, $articleId)
    {
        $articleId = filter_var($articleId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$articleId) {
            header('Location: /admin');
            exit;
        }

        $db = $this->dbConnect();
        $article = $db->select()
                      ->from('article')
                      ->where('id = ' . $articleId)
                      ->where('user_id = ' . $userId)
                      ->query()
                      ->fetchAll();
        if (!$article) {
            throw new InvalidArgumentException("Article don\'t exists or illegal user !");
        }
        $tagsMid = $db->select()
                      ->from('tag_mid', 'tag_id')
                      ->where('article_id = ' . $articleId)
                      ->query()
                      ->fetchAll();
        if ($tagsMid) {
            $tagsId = array();
            foreach ($tagsMid as $tag) {
                $tagsId[] = $tag['tag_id'];
            }
            $sql = "SELECT name from tag WHERE id in (" . implode(',', $tagsId) . ")";
            $diffTags = $db->fetchAll($sql);
            $tagNames = array();
            foreach ($diffTags as $tag) {
                $tagNames[] = $tag['name'];
            }
            $finalTags = implode(',', $tagNames);
        } else {
            $finalTags = '';
        }
        $article[0]['tags'] = $finalTags;
        return $article[0];
    }

    public function editArticle($data, $userId, $articleId)
    {
        $articleId = filter_var($articleId, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)));
        if (!$articleId) {
            header('Location: /admin');
            exit;
        }
        $requiredKeys = array('column', 'title', 'tag');
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("Missing requied key $key");
            }
        }

        //column
        $column = filter_var($data['column'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$column) {
            throw new InvalidArgumentException('Column is invalid');
        }
        //title
        $title = trim($data['title']);
        if (empty($title)) {
            throw new InvalidArgumentException('Please fill the title');
        }
        $length = mb_strlen($title, "UTF-8");
        if ($length > 255) {
            throw new InvalidArgumentException('Title is over range(255)!');
        }

        //tag
        if (!empty($data["tag"])) {
            $tagsExplode = explode(',', $data['tag']);
            if (count($tagsExplode) >= 10) {
                throw new InvalidArgumentException("Don't use over 10 tags");
            }
            $tagsGet = array();
            foreach ($tagsExplode as $tag) {
                if ($tag) {
                    if (!in_array($tag, $tagsGet) && trim($tag) != '') {
                        $length = mb_strlen($tag, 'UTF-8');
                        if ($length > 32) {
                            throw new InvalidArgumentException('Some of tags is over range(32)!');
                        }
                        $tagsGet[] = trim($tag);
                    }
                }
            }
        } else {
            $tagsGet = array();
        }

        $db = $this->dbConnect();

        $result = $db->select()
                     ->from('article', 'is_link')
                     ->where('id = ' . $articleId)
                     ->where('user_id = ' . $userId)
                     ->query()
                     ->fetchAll();
        if ($result) {
            if ($result[0]['is_link'] == 0) {
                if (!isset($data['formaltext'])) {
                    throw new InvalidArgumentException('Missing requied key formaltext');
                }
                $formaltext = $data['formaltext'];
                if (empty($formaltext)) {
                    throw new InvalidArgumentException('The formaltext can not be empty');
                }
                $length = strlen($formaltext);
                if ($length > 65534) {
                    throw new InvalidArgumentException('Formaltext is over range(65535)!');
                }
                $link = '';
            }

            if ($result[0]['is_link'] == 1) {
                if (!isset($data['link'])) {
                    throw new InvalidArgumentException('Missing requied key link');
                }
                $link = $data['link'];
                if (empty($link)) {
                    throw new InvalidArgumentException('The link can not be empty');
                }
                $link = filter_var($link, FILTER_VALIDATE_URL);
                if (!$link) {
                    throw new InvalidArgumentException('Link is invalid');
                }
                $length = mb_strlen($link, 'UTF-8');
                if ($length > 2000) {
                    throw new InvalidArgumentException('Link is over range(2000)!');
                }
                $formaltext = '';
            }
        } else {
            throw new InvalidArgumentException("It's not your article");
        }

        // SELECT tag_mid for article
        $tagsMid =  $db->select()
                       ->from('tag_mid')
                       ->where('article_id = ' . $articleId)
                       ->query()
                       ->fetchAll();
        $tagsMidId = array();
        if ($tagsMid) {
            foreach ($tagsMid as $tagMid) {
                $tagsMidId[] = $tagMid['tag_id'];
            }
        }

        $db->beginTransaction();

        try {
            // UPDATE article
            $db->update('article', array(
                    'title'      => $title,
                    'formaltext' => $formaltext,
                    'column'     => $column,
                    'link'       => $link
            ), 'id = ' . $articleId);

            // If no tags post && tag_mid have data, delete it.
            if (empty($tagsGet) && !empty($tagsMidId)) {
                // DELETE whole tag_mid, finish.
                $db->delete('tag_mid', 'article_id = ' . $articleId);
            }
            if ($tagsGet) {
                $sql = "SELECT * FROM tag WHERE name in (?".str_repeat(',?', count($tagsGet)-1).")";
                $tagsHave = $db->fetchAll($sql, $tagsGet);
                
                // Tag table operation, return $tagsId
                $tagsId = array();
                if (empty($tagsHave)) {
                    // Insert all tags
                    foreach ($tagsGet as $tagGet) {
                        $db->insert('tag', array('name' => $tagGet));
                        $tagsId[] = $db->lastInsertId();
                    }
                } else {
                    $tagsHaveName = array();
                    foreach ($tagsHave as $tagHave) {
                        $tagsId[] = $tagHave['id'];
                        $tagsHaveName[] = $tagHave['name'];
                    }
                    $tagsInsert = array_diff($tagsGet, $tagsHaveName);
                    if ($tagsInsert) {
                        // Insert tags which is not in DB
                        foreach ($tagsInsert as $tagInsert) {
                            $db->insert('tag', array('name' => $tagInsert));
                            $tagsId[] = $db->lastInsertId();
                        }
                    }
                }
                // Modify tag_mid
                $tagsMidInsert = array_diff($tagsId, $tagsMidId);
                $tagsMidDelete = array_diff($tagsMidId, $tagsId);
                if ($tagsMidInsert) {
                    foreach ($tagsMidInsert as $tagMidInsert) {
                        $db->insert('tag_mid', array(
                            'tag_id'     => $tagMidInsert,
                            'article_id' => $articleId
                        ));
                    }
                }
                if ($tagsMidDelete) {
                    foreach ($tagsMidDelete as $tagMidDelete) {
                        $db->delete('tag_mid', 'tag_id = ' . $tagMidDelete . ' and article_id = ' . $articleId);
                    }
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
