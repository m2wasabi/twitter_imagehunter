<?php
require_once('config.php');

function connectTwitter($url){
    try {
        $oauth = new OAuth(TwitterKeys::CONSUMER_KEY, TwitterKeys::CONSUMER_SECRET, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_AUTHORIZATION);
        $oauth->setToken(TwitterKeys::ACCESS_TOKEN, TwitterKeys::ACCESS_TOKEN_SECRET);
        $oauth->fetch($url);
        return json_decode( $oauth->getLastResponse(), TRUE);
    } catch (OAuthException $E){
        echo "Exception caught!\n";
        echo "Response: ". $E->lastResponse . "\n";
    }
    return null;
}

function mediaTracker($data){
    if(!is_array($data)) return;
    foreach($data as $entry){
        echo "Reading ID : ". $entry['id'] . "\n";
        echo "Created At : ". $entry['created_at'] . "\n";
        if(isset($entry['retweeted_status']['extended_entities']['media']) && count($entry['retweeted_status']['extended_entities']['media'])){
            $user = $entry['retweeted_status']['user']['screen_name'];
            @mkdir(BASEDIR . $user);
            dl_media($user, $entry['retweeted_status']['extended_entities']['media']);
        }elseif(isset($entry['extended_entities']['media']) && count($entry['extended_entities']['media'])){
            $user = $entry['user']['screen_name'];
            @mkdir(BASEDIR . $user);
            dl_media($user, $entry['extended_entities']['media']);
        }elseif(isset($entry['retweeted_status']['entities']['media']) && count($entry['retweeted_status']['entities']['media'])){
            $user = $entry['retweeted_status']['user']['screen_name'];
            @mkdir(BASEDIR . $user);
            dl_media($user, $entry['retweeted_status']['entities']['media']);
        }elseif(isset($entry['entities']['media']) && count($entry['entities']['media'])){
            $user = $entry['user']['screen_name'];
            @mkdir(BASEDIR . $user);
            dl_media($user, $entry['entities']['media']);
        }
    }
}

function dl_media($user, $medias){
    foreach($medias as $media){
        $dl_file = BASEDIR . $user . '/' . basename($media['media_url']);
        if(!file_exists($dl_file)){
            echo "File Download " . $media['media_url'] . "  by " . $user . "\n";
            $fp = fopen($dl_file, 'wb');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $media['media_url'] . ':orig');

            curl_setopt($ch, CURLOPT_HTTPGET,        true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FAILONERROR,    false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_ENCODING ,      'gzip');
            curl_setopt($ch, CURLOPT_FILE, $fp);

            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }
    }
}
