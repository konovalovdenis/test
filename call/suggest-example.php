<?php
require('SuggestClient.php');
use Dadata\SuggestClient as SuggestClient;
$token = '30639698ce967afd6652aceb259953942b04c599';
$dadata = new SuggestClient($token);
$query = "230209309807";
$data = array(
    'query' => $query
);
$resp = $dadata->suggest("party", $data);
print "Запрос: " . $query . "\n<br>";
print "Ответ: \n";
foreach ($resp->suggestions as $suggestion) {
    print $suggestion->unrestricted_value . "\n";
}
?>