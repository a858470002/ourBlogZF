<?php
return array(
    "article"=>array(
        array(
            "id"         => 1,
            "title"      => "test article",
            "formaltext" => "wojiushi zhengwen",
            "column"     => 1,
            "user_id"    => 1,
            "link"       => '',
            "is_link"    => 0
            ),
        array(
            'id'         => 2,
            'title'      => "testTitle'or''",
            'formaltext' => "testFormaltext'or''",
            'column'     => 1,
            'user_id'    => 1,
            'link'       => '',
            'is_link'    => 0
            )
    ),
    "tag"=>array(
        array("id"=>1, "name"=>"php"),
        array("id"=>2, "name"=>"java")
    ),
    "tag_mid"=>array(
        array("id"=>1, "tag_id"=>1, "article_id"=>1),
        array("id"=>2, "tag_id"=>2, "article_id"=>1),
        array("id"=>3, "tag_id"=>1, "article_id"=>2),
        array("id"=>4, "tag_id"=>2, "article_id"=>2)
    ));
