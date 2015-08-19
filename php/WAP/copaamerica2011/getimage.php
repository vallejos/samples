<?php
  $path = $_GET['path'];
  $file = file($path);
  $bin = implode($file);
  /*
  $fp = fopen($path,"r");
  $bin = fread($fp,5000000);
  fclose($fp);
  */
  header("content-type: image/gif");
  echo $bin;
?>
