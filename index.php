<?php

require_once("./config.php");
header('Content-Type: application/json');



//синтаксис запроса:
//?fromnum=<fromnum>&tonum=<tonum>dtmf=<dtmf>&label=<label>&time=<time>
//Передаваемые параметры:
//fromnum - с какого номера пришёл вызов.
//tonum - на какой номер пришёл вызов
//dtmf - если перед функцией звонок попал на узел голосовое меню и звонящий
//набрал в нём какое нибудь число, оно будет передано в параметре
//label - если указан текст метки, он будет передан в параметре
//time - время прихода вызова в АТС
//Ответ: {"choice":1,"name":"sipuni call", "number":"100"}

$query = $res->query("SELECT * FROM `call` WHERE fromnum like '%".substr($_GET['fromnum'], 1, 12)."%' ORDER BY ISN DESC LIMIT 1");
$name="";
while ($row = $query->fetch()){
	$name=$row['name'];
	$isn_klient=$row['isn_klient'];
	$manager=$row['manager'];
}
if ($isn_klient=="") {
	if (($manager=="") or ($manager=="---")) { $number='';} else { $number=',"number":"'.$manager.'"';}
} else {
	$query = $res->query("SELECT * FROM `call` WHERE isn_klient='".$isn_klient."' and ((label='sale') or (label='support')) ORDER BY ISN DESC LIMIT 1");
	while ($row = $query->fetch()){
		$name=$row['name'];
		$manager=$row['manager'];
	}
	if (($manager=="") or ($manager=="---")) { $number='';} else { $number=',"number":"'.$manager.'"';}
}



if ($_GET['tonum']=='78612117108') {$tonum=" отдела продаж ";} else  {$tonum=" отдела поддержки ";}
if ($_GET['fromnum']<>"") { 
$query = $res->query("SELECT * FROM klients WHERE number_telefone like '%".substr($_GET['fromnum'], 1, 12)."%' LIMIT 1");
$count = $query->rowCount();
if ($count==1) {
	while ($row = $query->fetch()){
		$isn_klient=$row['isn'];
		$zamena = array("ООО", "ОАО","»","«");
		$name_organizaciya = str_replace($zamena, "", $row['name_organizaciya']);
		echo '{"choice":0,"name":"'.$name_organizaciya.'"'.$number.'}';
	}

} else {
	$query = $res->query("SELECT * FROM journal WHERE registration_telefone like '%".substr($_GET['fromnum'], 1, 12)."%' LIMIT 1");
	$count = $query->rowCount();
	//if ($count==1) {
		while ($row = $query->fetch()){
			$isn=$row['isn'];
			$num_licenses=$row['num_licenses'];
			$zamena = array("ООО", "ОАО","»","«");
			$registration_organizaciya = str_replace($zamena, "", $row['registration_organizaciya']);
			$tel_licenses='Лицензии^javascript:findtel(\"'.substr($_GET['fromnum'], 1, 12).'\");';
			if ($count==1)  {echo '{"choice":0,"name":"'.$registration_organizaciya.'"'.$number.'}';}
			$no_print=1;
		}
			
	//} else {
		$query = $res->query("SELECT * FROM `call` WHERE fromnum like '%".substr($_GET['fromnum'], 1, 12)."%' LIMIT 1");
		
		while ($row = $query->fetch()){
			if ($row['isn_klient']<>null) {
				$isn_klient=$row['isn_klient'];
				$query1= $res->query("SELECT * FROM klients WHERE isn='".$isn_klient."' LIMIT 1");
				while ($row1 = $query1->fetch()){
					$zamena = array("ООО", "ОАО","»","«");
					$name_organizaciya = str_replace($zamena, "", $row1['name_organizaciya']);
					if ($no_print<>1) echo '{"choice":0,"name":"'.$name_organizaciya.'"'.$number.'}';
					$update="yes";
				}	
			}
		}
	//}
}

if ($isn_klient<>'') {
	$print1_1=",isn_klient";
	$print1_2=",'".$isn_klient."'";
	$query =  $res->query("INSERT INTO registration(datetime,name,isn_klient,type) VALUES (NOW(),'Клиент ".$name_organizaciya." позвонил на номер ".$tonum."!','".$isn_klient."','Звонок')");
} 

if ($num_licenses<>'') {
	$print2_1=",licenses";
	$print2_2=",'".$tel_licenses."'";
}

if (($num_licenses=='') && ($num_licenses=='')) {
	$result = file_get_contents('https://search-maps.yandex.ru/v1/?apikey=f2ee3c6a-4b6f-4bf8-8efe-8d0ef02ebd6f&text='.substr($_GET['fromnum'], 1, 12).'&type=biz&lang=ru_RU');
	$json = json_decode($result);
	if ($name=="") $name = implode("/",array($json->features[0]->properties->CompanyMetaData->name));
}

$zamena = array("%3A");
$time= str_replace($zamena, ":", $_GET['time']);

$zamena = array("+");
$time= str_replace($zamena, " ", $time);

$query = $res->query("INSERT INTO `call`(time_server,fromnum,tonum,name,manager,label,importance,time".$print1_1.$print2_1.") VALUES (NOW(),'".$_GET['fromnum']."','".$_GET['tonum']."','".$name."','".$manager."','".$_GET['label']."','1','".$time."'".$print1_2.$print2_2.");");

//$d1 = strtotime($_GET['time']); // переводит из строки в дату
//$date = date("Y-m-d H:m:s", $d1); // переводит в новый формат

$file = 'call.txt';
$current = file_get_contents($file);
$current .= $_GET['time'];
file_put_contents($file, $current);


if ($update=="yes") {
	$query =  $res->query("UPDATE `call` SET  isn_klient='".$isn_klient."' where fromnum like '%".substr($_GET['fromnum'], 1, 12)."%'");
}

} else { print "Гуляй лесом!";}
?>