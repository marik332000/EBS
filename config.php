<?php
  ob_start();
  date_default_timezone_set('UTC');
  function f4db($str) {
    $str = htmlspecialchars($str);
    return mysql_real_escape_string($str);
  }
  define('MYSQL_HOST',     'localhost');
  define('MYSQL_USER',     'root');
  define('MYSQL_PASS',     'root');
  define('MYSQL_DATABASE', 'ebs');
  @mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) OR die("connect: ".mysql_error());
  mysql_select_db(MYSQL_DATABASE) OR die("select: ".mysql_error());
?>
