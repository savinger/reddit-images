<?php
  
function saveImage($url) {
    
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
  $raw = curl_exec($ch);
  curl_close($ch);
  
  $saveto = 'img/'.array_pop(explode('/', $url));
  if (file_exists($saveto)) {
    unlink($saveto);
  }
  $fp = fopen($saveto, 'x');
  fwrite($fp, $raw);
  fclose($fp);
}

if (isset($argv[1]) && isset($argv[2]) && is_int(intval($argv[2]))) {

  require_once("reddit.php");
  $reddit = new reddit('basic');
  
  $count = 0;
  $params = array('limit'=>50);
  while ($count < $argv[2]) {
    $response = $reddit->getListing($argv[1], $params);
    if (is_array($response->data->children)) {
      foreach ($response->data->children as $link) {
        $url = $link->data->url;
        if (preg_match('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $url) === 1) {
          saveImage($url);
          ++$count;
        }
        $params['after'] = $link->data->name;
      }
    } else {
      echo "Something didn't work.\n";
    }
  }
  
  
  
} else {
  
  echo "Usage: php reddit_imgs.php SUBREDDIT NUMBER_OF_IMAGES\n";
  
}

?>