<?php
require_once('config.php');
require_once('twitter_lib.php');

define('ONCE_COUNT',200);


if(isset($argv[1]) && $argv[1]){
    $target_user = $argv[1];
}else{
    $target_user = 'funassyi';
}

define('BASEDIR', realpath('.') . PATH_SEPARATOR . $target_user . PATH_SEPARATOR);
@mkdir(BASEDIR);

$max_id = null;

while(true){
    $data = userTwitter($target_user,$max_id);
    mediaTracker($data);
    $_tmp = end($data);
    $max_id = $_tmp['id'];
    if(count($data) <= 1) break;
}
exit(0);
///////////////////

function userTwitter($target_user,$max_id = null){
    $_m = ($max_id)? '&max_id='.$max_id : '';
    $url = TwitterKeys::API_BASEPATH . 'statuses/user_timeline.json?screen_name=' . $target_user
        . '&count=' . ONCE_COUNT
        . '&exclude_replies=true'
        . '&include_rts=1'
        . $_m;
    return connectTwitter($url);
}
