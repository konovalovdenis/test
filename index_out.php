<?php

require_once("./config.php");
header('Content-Type: application/json');

print '{"choice":0}';

//fromnum - с какого номера пришёл вызов.
//tonum - на какой номер пришёл вызов
//dtmf - если перед функцией звонок попал на узел голосовое меню и звонящий набрал в нём какое нибудь число, оно будет передано в параметре
//label - если указан текст метки, он будет передан в параметре
//time - время прихода вызова в АТС

$query = $res->query("SELECT * FROM klients WHERE number_telefone like '%".substr($_GET['tonum'], 1, 11)."%' LIMIT 1");
$count = $query->rowCount();

if ($count==1) {
	while ($row = $query->fetch()){
		$isn_klient=$row['isn'];
	}

} else {
	$query = $res->query("SELECT * FROM `call` WHERE fromnum like '%".substr($_GET['tonum'], 1, 11)."%' LIMIT 1");	
	while ($row = $query->fetch()){
		if ($row['isn_klient']<>null) {
			$isn_klient=$row['isn_klient'];
		}
	}
}

$query = $res->query("INSERT INTO `call`(time_server,isn_klient,fromnum,label,time,importance) VALUES (NOW(),'".$isn_klient."','".$_GET['tonum']."','".$_GET['label']."','".$_GET['time']."',1)");

?>