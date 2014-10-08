<?php 
include 'tmhOAuth.php';
include 'tmhUtilities.php';

function f4db($str) {
  $str = htmlspecialchars($str);
  return mysql_real_escape_string($str);
}

function gettl($user) {

  $tmhOAuth = new tmhOAuth(array(
    'consumer_key' => CKEY,
    'consumer_secret' => CSECRET,
    'user_token' => ATOKEN,
    'user_secret' => ASECRET,
    'curl_ssl_verifypeer' => false,
    'curl_proxy' => PROXYHOST.':'.PROXYPORT,
  ));

  $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'), array( 'screen_name' => $user, 'count' => '50')); $response = $tmhOAuth->response['response']; $timeline = json_decode($response, true);
  $i = 0;
  $max = "";

  foreach ($timeline as $s) {
    $max = $s['id_str'];
  if($i%2 == 0) { $class = 'odd'; } else { $class = 'even'; }
    $timestamp = $s["created_at"];
    $date = explode(" ", $timestamp);
    $time = explode(':',$date[3]);
    $datef = mktime($time[0],$time[1],$time[2],date("m", strtotime($date[1])),$date[2],$date[5]);
    $ago = time_since($datef);
    $status = $s["text"];       
    $status = preg_replace('/([\w]+\:\/\/[\w-?&;#~=.\/\@]+[\w\/])/', '<a class="external" target="_blank" href="index.php?p=link&url=$1">$1</a>', $status);
    $status = preg_replace('/#([A-Za-z0-9_ÄÖÜßäöü\/.]*)/', '<a class="external" href="index.php?p=search&q=%23$1">#$1</a>', $status);
    $status = preg_replace('/@([A-Za-z0-9_\/.]*)/', '<a class="external" href="index.php?p=search&q=%40$1">@$1</a>', $status);
    echo '<div class="mdiv '.$class.'"><span class="meta"><a class="external" href="index.php?p=timeline&u='.$user.'">@'.$user.'</a> - '.$ago.' <!--'.$max.'--></span><p class="msg">'.$status.'<br>';
    if ($s['entities']['media'][0]['media_url']!="") { 
      $img = substr($s['entities']['media'][0]['media_url'],27);
      echo '<a href="getimg.php?view='.$img.'"><img src="getimg.php?thumb='.$img.'"/></a></p>'; 
    }
    echo '</div>';
    $i++;
   }

  $max = $max-1;
  $max = (string)$max;

  echo "\n<div class=\"link\"><p>";
  echo "<a href=\"index.php?p=timeline&u=$user\">refresh</a>";
  //echo "&nbsp;|&nbsp;";
  //echo "<a href=\"index.php?p=timeline&u=$user&max=$max\">more</a>";
  echo "</p></div>\n";
}

function getlocal() {
  if (isset($_GET['page']) && is_numeric($_GET['page'])) { $page = $_GET['page']; } else { $page = 1; }
 
  $total = mysql_num_rows(mysql_query("SELECT id, tstamp FROM ebs WHERE (".time()." - tstamp <= 86400) OR (trip = 'admin')"));
  $per_page = 20;
  $last = ceil($total/$per_page);
 
  if ($page < 1) { $page = 1; } elseif ($page > $last) { $page = $last; }
 
  $max = 'LIMIT ' .($page - 1) * $per_page .',' .$per_page;
  $lm = mysql_query("SELECT * FROM ebs WHERE (".time()." - tstamp <= 86400) OR (trip = 'admin') ORDER BY id DESC $max"); 
  $i = 0;

  while($messages = mysql_fetch_assoc($lm)) {
    if($i%2 == 0) { $class = 'odd'; } else { $class = 'even'; }
    if (!empty($messages['trip'])) { $tripper = "!".$messages['trip']; } else { $tripper = ""; }
    $ago = time_since($messages['tstamp']);
    $status = twittertags($messages['content']);
    echo "<div class=\"mdiv $class\"><span class=\"meta\" title=\"posted @ ".date("Y-m-d H:i T", $messages['tstamp'])."\">Anonymous$tripper - $ago</span><p class=\"msg\">".$status."</p></div>\n";
    $i++;
  }
 
  if ($total > $per_page) {
    echo "\n<div class=\"link\"><p>";
    if ($page > 1) { $previous = $page-1; echo "<a href=\"index.php?p=local&page=$previous\">newer messages</a>"; }
    if ($page != 1 && $page != $last) { echo "&nbsp;|&nbsp;"; }
    if ($page < $last) { $next = $page+1; echo "<a href=\"index.php?p=local&page=$next\">older messages</a>"; }
    echo "</p></div>\n";
  }
}

function search($term, $max_id=NULL) {

  $tmhOAuth = new tmhOAuth(array(
    'consumer_key' => CKEY,
    'consumer_secret' => CSECRET,
    'user_token' => ATOKEN,
    'user_secret' => ASECRET,
    'curl_ssl_verifypeer' => false,
    'curl_proxy' => PROXYHOST.':'.PROXYPORT,
  ));

  $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/search/tweets'), array( 'q' => $term, 'result_type' => 'mixed', 'count' => '50', 'max_id' => $max_id, 'include_entities' => 'true')); $response = $tmhOAuth->response['response']; $timeline = json_decode($response, true);
  $i = 0;
  $max = "";

  foreach ($timeline['statuses'] as $s) {
    $max = $s['id_str'];
    if($i%2 == 0) { $class = 'odd'; } else { $class = 'even'; }
    $user = $s["user"]["screen_name"];
    $timestamp = $s["created_at"];
    $date = explode(" ", $timestamp);
    $time = explode(':',$date[3]);
    $datef = mktime($time[0],$time[1],$time[2],date("m", strtotime($date[1])),$date[2],$date[5]);
    $ago = time_since($datef);
    $dateg = strtotime($date[0].", ".$date[1]." ".$date[2]." ".$date[5]." - ".$date[3]." UTC");
    $status = $s["text"];   
    //todo: replace t.co with long urls
    $status = preg_replace('/([\w]+\:\/\/[\w-?&;#~=.\/\@]+[\w\/])/', '<a class="external" target="_blank" href="index.php?p=link&url=$1">$1</a>', $status);
    $status = preg_replace('/#([A-Za-z0-9_ÄÖÜßäöü\/.]*)/', '<a class="external" href="index.php?p=search&q=%23$1">#$1</a>', $status);
    $status = preg_replace('/@([A-Za-z0-9_\/.]*)/', '<a class="external" href="index.php?p=search&q=%40$1">@$1</a>', $status);
    echo '<div class="mdiv '.$class.'"><span class="meta"><a class="external" href="index.php?p=timeline&u='.$user.'">@'.$user.'</a> - '.$ago.' <!--'.$max.'--></span><p class="msg">'.$status.'<br>';
    if ($s['entities']['media'][0]['media_url']!="") { 
      $img = substr($s['entities']['media'][0]['media_url'],27);
      echo '<a href="getimg.php?view='.$img.'"><img src="getimg.php?thumb='.$img.'"/></a></p>';
    }
  echo '</div>';
  $i++;
  }

  echo "\n<div class=\"link\"><p>";
  echo "<a href=\"index.php?p=search&q=$term\">refresh</a>";
  //echo "&nbsp;|&nbsp;";
  //echo "<a href=\"index.php?p=search&q=$term&max=".($max-1)."\">more</a>";
  echo "</p></div>\n";
}

function maketrip($code) {
  #example function-replace with your own
  $trip_length = -10; 
  $salt = "taoW3wq22jO0sqywh";
  $trip = crypt($trip.$salt);
  $trip = substr($trip,$trip_length); 
  return $trip;
}

function time_since($time) {
  $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
  $lengths = array("60","60","24","7","4.35","12","10");
  $now = time();
  $difference = $now - $time;
  $tense = "ago";
  for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
    $difference /= $lengths[$j];
  }
  $difference = round($difference);
  if($difference != 1) {
    $periods[$j].= "s";
  }
  return "$difference $periods[$j] ago";
}

function twittertags($status) {
  $status = preg_replace('/(^|\s)@(\p{L}+)/', '\1<a class="external" href="index.php?p=search&q=%40\2" title="lookup on twitter" target="blank">@\2</a>', $status);
  return preg_replace('/(^|\s)#(\p{L}+)/', '\1<a class="external" href="index.php?p=search&q=%23\2" title="lookup on twitter" target="blank">#\2</a>', $status);
}

function get_url($shortURL) {

  $url = "http://www.longurlplease.com/api/v1.1?q=" . $shortURL;
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_PROXY, PROXYHOST.':'.PROXYPORT);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  $url_json = curl_exec($ch);
  curl_close($ch);

  $url_array = json_decode($url_json,true);

  $url_long = $url_array["$shortURL"];

  if ($url_long == null) {
    return $shortURL;
  }
  return $url_long;
}
?>
