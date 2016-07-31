<?php 
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

$num1 = (int) Base::super_post_text('num1');
$num2 = (int) Base::super_post_text('num2');
// var_dump( $num1);

echo $num1 + $num2;