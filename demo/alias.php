<?php 
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new SyncConsumer($server); //同步
//$consumer = new BatchConsumer($server); // 批量
$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(2);


$distinctId = '1234567890987654321';
$registerId  = 'ABCDEF123456789';
$platform = 'JS';
$ans->alias($registerId,$distinctId,$platform);

//$ans->flush() //批量
 ?>