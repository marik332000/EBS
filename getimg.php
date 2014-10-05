<?php
if (isset($_GET['thumb']) && $_GET['thumb']!="") {
    $url = 'http://pbs.twimg.com/media/'.urlencode($_GET['thumb']).':thumb';
    $type = substr($_GET['thumb'],-3);
    if ($type == 'png') {
    header('Content-type: image/png');
    imagepng(imagecreatefrompng($url));
    } elseif ($type == 'jpg') {
    header('Content-type: image/jpeg');
    imagejpeg(imagecreatefromjpeg($url));
    }
}

elseif (isset($_GET['view']) && $_GET['view']!="") {
    $url = 'http://pbs.twimg.com/media/'.urlencode($_GET['view']).'';
    $type = substr($_GET['view'],-3);
    if ($type == 'png') {
    header('Content-type: image/png');
    imagepng(imagecreatefrompng($url));
    } elseif ($type == 'jpg') {
    header('Content-type: image/jpeg');
    imagejpeg(imagecreatefromjpeg($url));
    }
}
?>
