<?php

if (!isset($_SESSION)){session_start();}

//error_reporting(0);

$sql_server="localhost";
$sql_user = "root";
$sql_pass = "";
$sql_db = "license";
$type_db='PDO';

$res = new PDO('mysql:host='.$sql_server.';dbname='.$sql_db,$sql_user,$sql_pass, array(PDO::ATTR_PERSISTENT => true));
$res->exec("set names utf8");

date_default_timezone_set('Europe/Moscow');

function generatePassword($length = 10){
  $chars = 'abdefghknqrstyzABDEFGHKNQRSTYZ123456789';
  $numChars = strlen($chars);
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= substr($chars, rand(1, $numChars) - 1, 1);
  }
  return $string;
}


?>