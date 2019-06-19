<?php 
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new SyncConsumer($server); //同步
//$consumer = new BatchConsumer($server); // 批量
$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(2);
$testLang = 's123123123123';
for ($x=0; $x<=10; $x++) {
  $testLang=$testLang.$testLang;
}
$properties = array(
    'userLevel'=>0,
    'userPoint'=>0,
    'testLang'=>$testLang
);
$ans->registerSuperProperties($properties);

//$ans->flush() //批量
 ?>