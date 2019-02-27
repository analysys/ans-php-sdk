<?php 
require_once '../AnalysysAgent_PHP_SDK.php';

$appid = '9421608fd544a65e';
$server = 'https://arksdk.analysys.cn:4089/';
$consumer = new BatchConsumer($server); //批量
//$consumer = new SyncConsumer($server); // 同步
$ans = new AnalysysAgent($consumer, $appid);
$ans->setDebugMode(2);


$distinctId = '1234567890987654321';
$isLogin = true;
$eventName  = 'eventName';
$platform = 'JS';
$bookList = array(
	'is AnalysysAgent PHP SDK'
);
$track_properties = array(
	'$ip'=>'112.112.112.112',
	'productType'=>'PHP书籍',
	'productName'=>'bookList',
	'producePrice'=>'60',
	'shop'=>'在线'
);
$ans->track($distinctId,$isLogin,$eventName,$track_properties ,$platform);


$registerId  = 'ABCDEF123456789';
$ans->alias($registerId,$distinctId,$platform);

$fileSet_properties = array(
	'$city'=>'北京',
	'$province'=>'北京',
	'nickName'=>'昵称123',
	'userLevel'=>0,
	'userPoint'=>0,
	'interest'=>array(
		'户外活动',
		'足球赛事',
		'游戏'
	)
);
$ans->profileSet($registerId,$isLogin,$fileSet_properties,$platform);

$fileSetOnce_properties = array(
	'registerTime'=>'20180101101010'
);
$ans->profileSetOnce($registerId,$isLogin,$fileSetOnce_properties,$platform);


$fileIncrement_properties = array(
	'userPoint'=>20
);
$ans->profileIncrement($registerId,$isLogin,$fileIncrement_properties,$platform);


$fileAppend_properties = array(
	'interest'=>array(
		'户外活动',
		'足球赛事',
		'游戏'
	)
);
$ans->profileAppend($registerId,$isLogin,$fileAppend_properties,$platform);


$ans->profileUnSet($registerId,$isLogin,"nickName",$platform);

$ans->profileDelete($registerId,$isLogin,$platform);

$registerSuperProperties_properties = array(
	'userLevel'=>0,
	'userPoint'=>0
);
$ans->registerSuperProperties($registerSuperProperties_properties);


$ans->unRegisterSuperProperty('userPoint');


$ans->clearSuperProperties();


$getSuperProperty = $ans->getSuperProperty('userLevel');
printf('getSuperProperty--->%s',$getSuperProperty);


$properties = $ans->getSuperProperties();
printf('getSuperProperties---->');
print_r($properties);
$ans->flush() //批量
 ?>