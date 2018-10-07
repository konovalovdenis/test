<?php

header('Content-Type: application/json');

function suggest($type, $fields)
    {
        $result = false;
        if ($ch = curl_init("http://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/$type"))
        {
             curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
             curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                 'Content-Type: application/json',
                 'Accept: application/json',
                 'Authorization: 30639698ce967afd6652aceb259953942b04c599'
              ));
             curl_setopt($ch, CURLOPT_POST, 1);
             // json_encode
             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
             $result = curl_exec($ch);
             $result = json_decode($result, true);
             curl_close($ch);
        }
        return $result;
    }


$result = suggest("party", array("query"=>"7602072372", "count"=>2));
$json = json_decode(json_encode($result),true);

print_r('Наименование: '.$json['suggestions']['0']['value'].'<br>');
print_r('КПП: '.$json['suggestions']['0']['data']['kpp'].'<br>');
print_r('Руководитель: '.$json['suggestions']['0']['data']['management']['name'].'<br>');
print_r('Должность: '.$json['suggestions']['0']['data']['management']['post'].'<br>');
print_r('ОГРН: '.$json['suggestions']['0']['data']['ogrn'].'<br>');
print_r('Адрес: '.$json['suggestions']['0']['data']['address']['unrestricted_value'].'<br>');
print_r('Город: '.$json['suggestions']['0']['data']['address']['data']['city'].'<br>');
print_r('Страна: '.$json['suggestions']['0']['data']['address']['data']['country'].'<br>');



print_r($json);


?>