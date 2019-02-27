<?php 
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new SyncConsumer($server); //同步
//$consumer = new BatchConsumer($server); // 批量
$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(2);


$registerId = 'ABCDEF123456789';
$isLogin = true;
$platform = 'JS';
$properties = array(
	'interest'=>array(
		'户外活动',
		'足球赛事',
		'游戏'
	)
);
$ans->profileAppend($registerId,$isLogin,$properties,$platform);

//$ans->flush() //批量
 ?>