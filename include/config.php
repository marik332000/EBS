<?php 
  date_default_timezone_set('UTC');

  define('CKEY', '');
  define('CSECRET', '');
  define('ATOKEN', '');
  define('ASECRET', '');

  define('MYSQL_HOST',     'localhost');
  define('MYSQL_USER',     'root');
  define('MYSQL_PASS',     'root');
  define('MYSQL_DATABASE', 'ebs');
  @mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) OR die("connect: ".mysql_error());
  mysql_select_db(MYSQL_DATABASE) OR die("select: ".mysql_error());

  define('HIDDENSERV', 'ebszigics62vhony.onion');
  define('DEFAULTUSER', 'EMERGENCYRELAY');
  define('DEFAULTSEARCH', '#TwitterIsBlockedInTurkey');
  define('ACCOUNTNAME', 'EMERGENCYRELAY');

  define('PROXYHOST', '127.0.0.1');
  define('PROXYPORT', '8118');

  define('IMGPATH', '/your/temp/path'); //no trailing slash!

  define('TRIPSALT', 'yoursalthere');
  include 'functions.php';
?>
