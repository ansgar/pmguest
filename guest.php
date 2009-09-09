<?php

$HandleActions['guest_store'] = 'HandleGuestStore';
$HandleAuth['guest_store'] = 'read';

$HandleActions['guest_delete'] = 'HandleGuestDelete';
$HandleAuth['guest_store'] = 'admin';

require_once("lib/Akismet.class.php");

function HandleGuestStore($pagename, $auth) {
    global $wpcom_api_key, $wpcom_home;
    $akismet = new Akismet($wpcom_home ,$wpcom_api_key);
    $akismet->setCommentAuthor($_POST['name']);
    $akismet->setCommentAuthorEmail($_POST['email']);
    $akismet->setCommentAuthorURL($_POST['url']);
    $akismet->setCommentContent($_POST['comment']);

    $itemurl = $pagename.date("Ymd")."-".uniqid();

    $akismet->setPermalink($itemurl);

    $page['name'] = $itemurl;
    $page['text']  = "----\n";
    $page['text'] .= (strlen($_POST['name'])>0) ? $_POST['name'] : "Unbekannt";
    if (strlen($_POST['email'])>0){
        $page['text'] .= " [[&#9993;->mailto:";
        $page['text'] .= $_POST['email'];
        $page['text'] .= "]]";
    }
    if (strlen($_POST['url'])>0){
        $page['text'] .= " [[&#10138;->";
        $page['text'] .= substr($_POST['url'],0,4)=="http" ? $_POST['url'] : "http://".$_POST['url'];
        $page['text'] .= "]]";
    }
    $page['text'] .= " schrieb am ";
    $page['text'] .= date("d.m.Y");
    $page['text'] .= ":\n\n";
    $page['text'] .= $_POST['comment'];
    $page['text'] .= $akismet->isCommentSpam() ? "(:spam: true:)" : "(:spam: false:)";
    $page['time'] = $Now;
    $page['host'] = $_SERVER['REMOTE_ADDR'];
    $page['agent'] = @$_SERVER['HTTP_USER_AGENT'];

    UpdatePage($page['name'],
               $page, 
               $page);
    
    HandleBrowse($pagename); 
}

function HandleGuestDelete($pagename, $auth) {
    global $WikiDir, $LastModFile;
    $page = RetrieveAuthPage($pagename, $auth, true, READPAGE_CURRENT);
    if (!$page) { Abort("?cannot delete $pagename"); return; }
    $WikiDir->delete($pagename);
    if ($LastModFile) { touch($LastModFile); fixperms($LastModFile); }
    Redirect(substr($pagename, 0, strlen($pagename)-22));
}
