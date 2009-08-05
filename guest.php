<?php

$HandleActions['guest'] = 'HandleGuest';
$HandleAuth['guest'] = 'read';

require_once("lib/Akismet.class.php");

function HandleGuest($pagename, $auth) {
    global $wpcom_api_key;
    $MyBlogURL = 'http://www.example.com/blog/';
    $akismet = new Akismet($MyBlogURL ,$wpcom_api_key);
    $akismet->setCommentAuthor('viagra-test-123');
    $akismet->setCommentAuthorEmail('');
    $akismet->setCommentAuthorURL('http://www.da.ru');
    $akismet->setCommentContent('Visit my site');
    $akismet->setPermalink('http://www.example.com/blog/alex/someurl/');

    if($akismet->isCommentSpam()){
        echo "Jo, das ist Spam";
    }else{
        echo "Nö, ist OK";
    }

    $page['name'] = "Guest.".date("Ymd")."-".uniqid();
    $page['text'] = "Tadda";
    $page['time'] = $Now;
    $page['host'] = $_SERVER['REMOTE_ADDR'];
    $page['agent'] = @$_SERVER['HTTP_USER_AGENT'];

    UpdatePage($page['name'],
               $page, 
               $page);
}
