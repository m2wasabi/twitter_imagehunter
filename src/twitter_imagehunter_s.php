<?php
require_once('config.php');
require_once('twitter_lib.php');

$filename_escape = ['.', '\\', '+', '*', '?', '^', '$', '{', '}', '=', '!', '<', '>', '|', ':', ';', '/',' '];
define('ONCE_COUNT',100);
define('LANG','ja');
define('LOCALE','ja');

if(isset($argv[1]) && $argv[1]){
    $word = $argv[1];
}else{
    $word = '#pixiv';
}

$basedir = str_replace($filename_escape,'_',$word);
define('BASEDIR', realpath('.') . DIRECTORY_SEPARATOR . $basedir . DIRECTORY_SEPARATOR);
@mkdir(BASEDIR);

$max_id = null;

while(true){
    $data = searchTwitter($word,$max_id);
    mediaTracker($data['statuses']);
    $_tmp = end($data['statuses']);
    $max_id = $_tmp['id'];
    if(count($data['statuses']) <= 1) break;
}
exit(0);
///////////////////

function searchTwitter($word,$max_id = null){
        $_m = ($max_id)? '&max_id='.$max_id : '';
//        $url = TwitterKeys::API_BASEPATH . 'statuses/user_timeline.json?screen_name=' . $target_user . '&count=' . $getcount . '&exclude_replies=true&include_rts=1'. $older;
        $url = TwitterKeys::API_BASEPATH . 'search/tweets.json?q=' . rawurlencode($word)
            . '&count=' . ONCE_COUNT
            . '&lang=' . LANG
            . '&locale=' . LOCALE
            . '&result_type=mixed'. $_m;
        return connectTwitter($url);
}
