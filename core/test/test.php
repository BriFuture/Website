<?php 
require 'path.php';

$db = Db::getInstance();
echo $db->argument_to_mysql('yes', false);