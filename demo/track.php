<?php 
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new SyncConsumer($server); //同步
//$consumer = new BatchConsumer($server); // 批量
$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(2);


$distinctId = '1234567890987654321';
$isLogin = true;
$eventName  = 'eventName';
$platform = 'JS';
$bookList = array(
	'is AnalysysAgent PHP SDK'
);
$properties = array(
	'$ip'=>'112.112.112.112',
	'productType'=>'PHP书籍',
	'productName'=>'bookList',
	'producePrice'=>'60',
	'shop'=>'在线'
);

$ans->track($distinctId,$isLogin,$eventName,$properties ,$platform);

//$ans->flush() //批量
 ?>