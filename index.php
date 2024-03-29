<?php
  include './include/config.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>+++ Emergency Broadcast System +++</title>
  <link href="style.css" rel="stylesheet" type="text/css" />
  <meta name="description" content="EBS - Emergency Broadcast System for people without access to social media">
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <meta name="robots" content="noindex, nofollow">
</head>
<body>
  <div id="ebs">
  <div id="nav">
    <span class="navi"><a href="./">Home</a> | <a href="?p=local">Local Messages</a> | <a href="?p=timeline">Twitter Relay</a> | <a href="?p=search">Twitter Search</a></span>
  </div>
  <div id="content">
  <?php if(isset($_GET['p'])) {
    if ($_GET['p'] == 'timeline') {
  ?>
   <?php if(isset($_GET['u']) && ($_GET['u']!="")) { 
$user = urlencode($_GET['u']); } else { $user = DEFAULTUSER; } ?>
  <?php if (isset($_POST['tweet'])) {
      $tmhOAuth = new tmhOAuth(array(
    'consumer_key' => CKEY,
    'consumer_secret' => CSECRET,
    'user_token' => ATOKEN,
    'user_secret' => ASECRET,
    'curl_ssl_verifypeer' => false,
    'curl_proxy' => PROXYHOST.':'.PROXYPORT,
  ));

    if (!empty($_FILES['image']['name'])) {
      $image = "{$_FILES['image']['tmp_name']};type={$_FILES['image']['type']};filename={$_FILES['image']['name']}";
      $response = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update_with_media.json'),array('media[]' => "@{$image}", 'status' => $_POST['tweet']." "),true,true);
      if ($response == 200) {
        echo "<!-- DEBUG <span class=\"alert\"><strong>[$response] Image successfully relayed to Twitter.</strong></span><br> -->";
      }
      else {
        echo "<span class=\"alert\"><strong>[".print_r($tmhOAuth->response['response'], true)."] There was an error relaying the image to Twitter.</strong></span><br>";  
      }
    }
    else {
      $response = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array('status' => $_POST['tweet']));
      if ($response == 200) {
        echo "<!-- DEBUG <span class=\"alert\"><strong>[$response] Message successfully relayed to Twitter.</strong></span><br> -->";
      }
      else {
        echo "<span class=\"alert\"><strong>[".print_r($tmhOAuth->response['response'], true)."] There was an error relaying the image to Twitter.</strong></span><br>";                 
      }
    }
  }
  ?>
    <div class="notice"><p><strong>Here you can relay your messages and/or images to Twitter.<br>Messages will appear on the <?php echo '@'.ACCOUNTNAME; ?> Twitter feed.<br>You can also see the last 50 tweets of a specific user, it defaults to <?php echo '@'.DEFAULTUSER; ?>.<br>Please only use it for important communication.</strong><?php if ($_SERVER['HTTP_HOST'] != HIDDENSERV) { echo "<br><br><strong>This site is also available as a TOR HIDDEN SERVICE at <a href=\"http://".HIDDENSERV."\">".HIDDENSERV."</a>!</strong>"; } ?><br><small><a href="about" style="font-size:12px">[read more]</a></small></p></div>
  <div id="texta">
    <form action="" method="post" enctype="multipart/form-data">
      <fieldset id="main">
        <legend>Please enter your message:</legend>
        <span id="tarea"><textarea id="status" name="tweet" rows="4" cols="70" maxlength="300" placeholder="Your message here."></textarea></span>
        <span id="but"><input type="submit" value="Tweet!" class="button"></span>
        <span id="addimg"><label>Attach Image:</label><input type="file" name="image" /></span>
        <span id="remaining">max. 140 characters</span>
      </fieldset>
    </form>
<br><br>

    <form action="" method="GET">
       <fieldset id="options">
       <legend>Show timeline of user:</legend>
       <span id="tarea"><input type="hidden" name="p" value="timeline"><input type="text" name="u" placeholder="<?php echo DEFAULTUSER; ?>">&nbsp;&nbsp;<input type="submit" value="Show" /></span>
       </fieldset>
    </form>

    </div>
    <div id="messages">
    <?php gettl($user);
    ?>
    </div>
  <?php }

  elseif ($_GET['p']=='search') {
  ?>
  <div class="notice"><p><strong>This is the Twitter mixed search displaying a mixed feed of 50 popular and most recent tweets.<br>The current default search is: <?php echo DEFAULTSEARCH; ?></strong><?php if ($_SERVER['HTTP_HOST'] != HIDDENSERV) { echo "<br><br><strong>This site is also available as a TOR HIDDEN SERVICE at <a href=\"http://".HIDDENSERV."\">".HIDDENSERV."</a>!</strong>"; } ?><br><small><a href="about" style="font-size:12px">[read more]</a></small></p></div>
  <?php if (!empty($_GET['q'])) { $term = urlencode($_GET['q']); } else { $term = urlencode(DEFAULTSEARCH); } ?>
    <div id="texta">
     <form action="" method="GET">
       <fieldset id="main">
       <legend>What are you looking for?</legend>
       <span id="tarea"><input type="hidden" name="p" value="search"><input type="text" name="q" placeholder="<?php echo DEFAULTSEARCH; ?>">&nbsp;&nbsp;<input type="submit" value="Search" /></span>
       </fieldset>
    </form>
   </div>
    <div id="messages">
    <?php search($term); ?>
    </div>

<?php
  }
  elseif ($_GET['p']=='local') {
  ?>

<?php if (!empty($_POST['content'])) {
        if (!empty($_POST['trip'])) {
          $trip = maketrip($_POST['trip']);
        } else {
          $trip = "";
        }
        if (mysql_query( "INSERT INTO ebs (tstamp, content, trip ) VALUES('".time()."', '".f4db($_POST['content'])."','".$trip."')")) {
          mysql_query ( "DELETE e FROM ebs AS e JOIN (SELECT id FROM ebs WHERE (trip != 'admin') ORDER BY id DESC LIMIT 1 OFFSET 1000) AS lim ON e.id < lim.id ;" );
          mysql_query ( "DELETE FROM ebs WHERE (trip != 'admin' AND ".time()." - tstamp > 86400)");
        }
        else echo mysql_error();
      }
?>
  <div class="notice"><p><strong>This is the local feed. Messages posted here are NOT relayed to Twitter.<br>Messages are posted ANONYMOUSLY, are NOT VERIFIED and can be SEEN BY ANYONE - be cautious!</strong><?php if ($_SERVER['HTTP_HOST'] != HIDDENSERV) { echo "<br><br><strong>This site is also available as a TOR HIDDEN SERVICE at <a href=\"http://".HIDDENSERV."\">".HIDDENSERV."</a>!</strong>"; } ?><br><small><a href="about" style="font-size:12px">[read more]</a></small></p></div>
<div id="texta">
  <form action="" method="post" enctype="multipart/form-data">
        <fieldset id="main">
          <legend>Please enter your message:</legend>
          <span id="tarea"><textarea id="status" name="content" rows="4" cols="70" maxlength="300" placeholder="Your message here."></textarea></span>
          <span id="but"><input type="submit" value="Broadcast!" class="button"></span>
          <span id="addimg"><label>Tripcode (optional):</label><input type="text" name="trip" class="text"></span>
          <span id="remaining">max. 300 characters</span>
        </fieldset>

        </fieldset>
      </form>        
</div>
    <div id="messages">
  <?php getlocal(); ?>
    </div>

<?php
  }
  elseif ($_GET['p']=='home') {
  ?>
    <div id="texta">
    <div class="notice">
      <h2>Welcome to the Emergency Broadcast System</h2>
      <p><img src="pic.jpg" /><br><br><a href="index.php?p=local">Post a message on the EBS</a><br><small>EBS’e bir mesaj gönder</small><br><br><a href="index.php?p=timeline">Post to Twitter via <?php echo '@'.ACCOUNTNAME; ?></a><br><small>Twitter’a <?php echo '@'.ACCOUNTNAME; ?> hesabı üzerinden mesaj at</small><br><br><a href="index.php?p=search">Search on Twitter</a><br><small>Twitter’da arama</small><?php if ($_SERVER['HTTP_HOST'] != HIDDENSERV) { echo "<br><br><strong>This site is also available as a TOR HIDDEN SERVICE at <a href=\"http://".HIDDENSERV."\">".HIDDENSERV."</a>!</strong>"; } ?></strong></p>
    </div>
    </div>
  <?php
  }
  elseif ($_GET['p']=='link') {
  ?>

<?php if (!empty($_GET['url']) && substr( $_GET['url'], 0, 4 ) === "http")
  {
  $url = $_GET['url'];
  $url = get_url($url);
  $url = get_url($url);
  $url = get_url($url);
  $realurl = get_url($url);
}
?>
<div id="texta">
    <div class="notice">
    <h2>Attention</h2>
    <p>By following this link your are leaving the Emergency Broadcast System website.<br><br><img src="owl2.jpg" /><br><br></p><h2>GOOD LUCK!</h2><p><br><br><a style="font-size:18px;" href="<?php echo $realurl; ?>"><?php echo $realurl; ?></a></p>
    </div>
</div>
  <div class="link">
    <a href="./">back to main page</a>
  </div>
  <?php
  } else {
  header('Location:index.php?p=home');
  }
  } else {
  header('Location:index.php?p=home');
  }?>
  </div>
  <div id="footer">
    <a href="about">about this service</a>
  </div>
</div>
</body>
</html>
