<?php

include 'include/config.php';

if (isset($_GET['thumb']) && $_GET['thumb']!="") {
    $url = 'https://pbs.twimg.com/media/'.urlencode($_GET['thumb']).':thumb';
    $type = substr($_GET['thumb'],-3);
    $p = '/your/temp/path/t_'.urlencode($_GET['view']);
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_PROXY, PROXYHOST.':'.PROXYPORT);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
      $rawdata=curl_exec ($ch);
      curl_close($ch);
      $fp = fopen($p,'w');
      fwrite($fp, $rawdata); 
      fclose($fp);
    $img = $p;
    if ($type == 'png') {
    header('Content-type: image/png');
    imagepng(imagecreatefrompng($img));
    } elseif ($type == 'jpg') {
    header('Content-type: image/jpeg');
    imagejpeg(imagecreatefromjpeg($img));
    }
}

elseif (isset($_GET['view']) && $_GET['view']!="") {
    $url = 'https://pbs.twimg.com/media/'.urlencode($_GET['view']).':orig';
    $type = substr($_GET['view'],-3);
    $p = '/your/temp/path/'.urlencode($_GET['view']);
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_PROXY, PROXYHOST.':'.PROXYPORT);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
      $rawdata=curl_exec ($ch);
      curl_close($ch);
      $fp = fopen($p,'w');
      fwrite($fp, $rawdata); 
      fclose($fp);
    $img = $p;
    if ($type == 'png') {
    header('Content-type: image/png');
    imagepng(imagecreatefrompng($img));
    } elseif ($type == 'jpg') {
    header('Content-type: image/jpeg');
    imagejpeg(imagecreatefromjpeg($img));
    }
}
?>
