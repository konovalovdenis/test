<?php

//require_once("./config.php");
//header('Content-Type: application/json');


//echo '{"choice":0,"name":"test"}';

//$file = 'call.txt';

//$current = file_get_contents($file);

//$current .= $_GET['time']." ".$_GET['fromnum']."\n";


//file_put_contents($file, $current);

$d1 = strtotime('2018-09-22+08%3A55%3A40'); // переводит из строки в дату
print date("Y-m-d H:m:s", $d1); // переводит в новый формат



?>