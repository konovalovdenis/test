<?php

require_once("config.php");


$user = '014087';
$from = date('d.m.Y');
$to = date('d.m.Y');
//$from = '28.05.2018';
//$to = '28.05.2018';
$type = '0';
$state = '0';
$tree = '';
$fromNumber = '';
$toNumber = '';
$toAnswer = '';
$anonymous = '1';
$firstTime = '0';
$secret = '0.e0mcj0f32l9';

$hashString = join('+', array($anonymous, $firstTime, $from, $fromNumber, $state, $to, $toAnswer, $toNumber, $tree, $type, $user, $secret));
$hash = md5($hashString);

$url = 'https://sipuni.com/api/statistic/export';
$query = http_build_query(array(
    'anonymous' => $anonymous,
    'firstTime' => $firstTime,
    'from' => $from,
    'fromNumber' => $fromNumber,
    'state' => $state,
    'to' => $to,
    'toAnswer' => $toAnswer,
    'toNumber' => $toNumber,
    'tree' => $tree,
    'type' => $type,
    'user' => $user,
    'hash' => $hash,
));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$output = curl_exec($ch);
curl_close($ch);

//header("Content-Disposition: attachment; filename=stat_$from-$to.csv");
//echo $output;
/*
array() { 
	[0]=> string(16) "Входящий" //Тип
	[1]=> string(14) "Отвечен" //Статус
	[2]=> string(19) "18.01.2018 14:51:49" //Время
	[3]=> string(23) "Отдел продаж" //Схема
	[4]=> string(12) "+74832783118" //Откуда
	[5]=> string(12) "+78612117108" //Куда
	[6]=> string(3) "220" //Кто ответил
	[7]=> string(3) "178" //Длительность звонка
	[8]=> string(3) "162" //Длительность разговора
	[9]=> string(2) "16" //Время ответа
	[10]=> string(0) "" //Оценка
	[11]=> string(17) "1516276309.775398" //ID записи
	[12]=> string(0) "" //Метка
	[13]=> string(0) "" //Теги
	[14]=> string(0) "" //ID заказа звонка
	[15]=> string(1) "1" //Запись существует
	[16]=> string(1) "1" //Новый клиент
	[17]=> string(0) "" //Состояние перезвона
	[18]=> string(0) "" //Время перезвона 
} 
*/

$fullcsv = array_map('str_getcsv', str_getcsv($output,"\n"));
//print_r($fullcsv);
$i=0;
foreach ($fullcsv as &$value) {
	if ($i>0) {
		foreach ($value as &$value1) {
			//print $value1."<br>";
			$array=str_getcsv($value1,";");
			//print $value1;

			$array4=str_replace("-", "", $array[4]);
			$array5=str_replace("-", "", $array[5]);
			if (strlen($array4)>10) {
				if ($array5=="+78612117108") {$otdel="sale";}
				if ($array5=="+78612117109") {$otdel="support";}
				$query = $res->query("Select * from `call` where fromnum like '%".substr($array4, 1, 12)."%'");
				while ($row = $query->fetch()){
					$isn=$row['isn'];
					if ($row['save']<>'1'){
						if ($row['label']==$otdel) {
							$d1 = new \DateTime($row['time_server']);
							$d2 = new \DateTime($array[2]);
							if (abs($d1->getTimestamp() - $d2->getTimestamp()) < 20) {
								$query =  $res->query("UPDATE `call` SET  status='".$array[1]."',otvetil='".$array[6]."',dlitelnost_z='".$array[7]."',dlitelnost_r='".$array[8]."',vremya_otv='".$array[9]."',id_record='".$array[11]."',save='1' where isn='".$isn."'");
							
							}
							
						}
					}
				}	
			}
			if (strlen($array4)<4) {
				$query = $res->query("Select * from `call` where fromnum like '%".substr($array5, 2, 12)."%'");
				while ($row = $query->fetch()){
					$isn=$row['isn'];
					if ($row['save']<>'1'){
							$d1 = new \DateTime($row['time_server']);
							$d2 = new \DateTime($array[2]);
							if (abs($d1->getTimestamp() - $d2->getTimestamp()) <= 20) {
								if ($array[3]=="Отдел поддержки") { $tonum="1008612117109";} else {$tonum="1008612117108";}
								$query =  $res->query("UPDATE `call` SET  otvetil='".$array[4]."',tonum='".$tonum."',status='".$array[1]."',dlitelnost_z='".$array[7]."',dlitelnost_r='".$array[8]."',vremya_otv='".$array[9]."',id_record='".$array[11]."',save='1' where isn='".$isn."'");
							}
					}
				}
			}	
		}
	}
	$i++;
}

print "ok";

?>