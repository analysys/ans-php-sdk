<?php
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new SyncConsumer($server); //同步
// $consumer = new BatchConsumer($server); // 批量
// $consumer = new FileConsumer('./log'); // 存文件

$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(2);

$properties = $ans->getSuperProperties();
print_r($properties);

//$ans->flush() //批量
