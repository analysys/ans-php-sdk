<?php
require_once 'AnalysysAgent_PHP_SDK.php';
$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new BatchConsumer($server);
$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(2);

$properties = array('aaa' => 111, 'ccc' => 'sssddd');
// $ans->registerSuperProperties($properties);

// $propertyName = 'aaa';
// $ans->unRegisterSuperProperty($propertyName);

// $ans->clearSuperProperties();

// $key = 'ccc';
// $ans->getSuperProperty($key);

$ans->track('5556666', false,'gaolaoshi', $properties);
// $ans->track('2122', true, $properties);
$ans->flush();

?>