<?php 
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}
echo $_SERVER['REMOTE_ADDR'];
