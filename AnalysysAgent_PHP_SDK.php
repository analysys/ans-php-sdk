<?php

define('ANALYSYSAGENT_SDK_VERSION', '4.0.11');
define('ANALYSYSAGENT_NO_DEBUG', 0);
define('ANALYSYSAGENT_OPENNOSAVE_DEBUG', 1);
define('ANALYSYSAGENT_OPENANDSAVE_DEBUG', 2);
define('ANALYSYSAGENT_SDK_LIB', 'PHP');

class AnalysysAgentException extends \Exception {
}

class AnalysysAgent {
	private $_appid;
	private $_consumer;
	private $_baseProperties;
	private $_xcontextSuperProperties;
	private $_win;

	public function __construct($consumer, $appId) {
		$this->_consumer = $consumer;
		$this->_appid = $appId;
		if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
			$this->_win = true;
		}
		$this->setBaseProperties();
		$this->clearXcontextSuperProperties();
	}

	private function setBaseProperties() {
		$this->_baseProperties = array(
			'$lib' => ANALYSYSAGENT_SDK_LIB,
			'$lib_version' => ANALYSYSAGENT_SDK_VERSION,
			'$debug' => ANALYSYSAGENT_NO_DEBUG,
		);
	}
	private function clearXcontextSuperProperties() {
		$this->_xcontextSuperProperties = array();
	}

	private function msectime() {
		list($msec, $sec) = explode(' ', microtime());
		return (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
	}

	private function is_debug() {
		return $this->_baseProperties['$debug'] === ANALYSYSAGENT_OPENNOSAVE_DEBUG || $this->_baseProperties['$debug'] === ANALYSYSAGENT_OPENANDSAVE_DEBUG;
	}
	private function isChina($s) {
		return preg_match("/[\x{4e00}-\x{9fa5}]/u", $s);
	}
	private function illegalChar($s) {
		return !preg_match("/^[a-zA-Z-_$][a-zA-Z_0-9-_$]*$/u", $s);
	}
	private function checkKeyOnce(&$s) {
		$lib = '$lib';
		$lib_version = '$lib_version';
		$platform = '$platform';
		$first_visit_time = '$first_visit_time';
		$debug = '$debug';
		$is_login = '$is_login';

		if (strcmp($s,$lib)==0|| strcmp($s,$lib_version)==0 || strcmp($s,$platform)==0 || strcmp($s,$first_visit_time)==0|| $s === $debug || $s === $is_login) {
			return array(
				'code' => 400,
				'msg' => 'Property value invalid, nonsupport value: $lib/$lib_version/$platform/$first_visit_time/$debug/$is_login',
			);
		}
		return array(
			'code' => 200,
		);
	}
	private function checkKey($key, $type = null) {

		if ((is_null($key) || gettype($key) !== 'string' || $this->isChina($key) || $this->illegalChar($key))&&$type!=='ID') {
			return array(
				'code' => 400,
				'msg' => '"' . $key . '"does not conform to naming rules!',
			);
		}

		if ($type !== 'ID' && $type !== 'NAME') {
			$errId = $this->checkKeyOnce($key);
			if ($errId['code'] === 400) {
				return $errId;
			}
		}

		if($type !== 'NAME'&&$type !== 'ID'){
			if (strlen($key) === 0 || strlen($key) > 125) {
				return array(
					'code' => 400,
					'msg' => 'The length of the key value string[' . $key . '] needs to be 1-125!',
				);
			}
		}
		if($type === 'NAME'){
			if (strlen($key) === 0 || strlen($key) > 99) {
				return array(
					'code' => 400,
					'msg' => 'The length of the key value string[' . $key . '] needs to be 1-99!',
				);
			}
		}
		if($type === 'ID'){
			if(is_null($key) || gettype($key) !== 'string' || $this->isChina($key) ){
				return array(
					'code' => 400,
					'msg' => '"' . $key . '"does not conform to naming rules!',
				);
			}
			if (strlen($key) === 0 || strlen($key) > 255) {
				return array(
					'code' => 400,
					'msg' => 'The length of the key value string[' . $key . '] needs to be 1-255!',
				);
			}
		}
		

		return array(
			'code' => 200,
		);
	}
	private function checkTime($xwhen){

		if($xwhen === null){
			return array(
				'code' => 200
			);
		}else{
			if(is_string($xwhen)){
				$xwhen = (int) $xwhen;
			}
			if(!is_numeric($xwhen)){
				return array(
					'code' => 400,
					'msg' => '$xwhen does not conform to Time rules!, type needs time stamp!',
				);
			}else if(strlen($xwhen)<13){
				return array(
					'code' => 400,
					'msg' => '$xwhen does not conform to Time rules!, length needs 13!',
				);
			}else{
				return array(
					'code' => 201
				);
			}


		}
	}
	private function checkArray($val) {

		if (count($val) === 0 || count($val) > 100) {
			return array(
				'code' => 400,
				'msg' => 'The length of the property value array needs to be 1-99!',
			);
		}

		foreach ($val as $key=>$value) {
			if (is_numeric($value)) {
				return array(
					'code' => 400,
					'msg' => 'Property value invalid, current type: String!',
				);
			}
			if(strlen($value) == 0 ){
				return array(
					'code' => 400,
					'msg' => 'The length of the property value string[' . $value . '] needs to be 1-255!',
				);
			}
			if (strlen($value) > 255) {
				return array(
					'code' => 60001,
					'msg' => 'The length of the property value string[' . $value . '] needs to be 1-255!',
					'key'=>$key,
					'value'=>$value
				);
			}
		}
		return array(
			'code' => 200,
		);

	}
	private function checkValue($val) {
		if (is_null($val)) {
			return array(
				'code' => 400,
				'msg' => 'Property value invalid, current type: String!',
			);
		}
		if(is_bool($val)){
			return array(
				'code' => 200,
			);
		}
		if(strlen($val) == 0 ){
			return array(
				'code' => 400,
				'msg' => 'The length of the property value string[' . $val . '] needs to be 1-255!',
			);
		}
		if (strlen($val) > 255) {
			return array(
				'code' => 60001,
				'msg' => 'The length of the property value string[' . $val . '] needs to be 1-255!',
			);
		}
		return array(
			'code' => 200,
		);
	}

	private function checkProperty($property,$type=null) {
		if (!is_array($property)) {
			$err = array(
				'code' => 400,
				'msg' => 'Property value invalid. current type: array!',
			);
			return $err;
		}

		foreach ($property as $key => $value) {
			$errKey = $this->checkKey($key, $type);
			if ($errKey['code'] === 400) {
				return $errKey;
			}

			if ($type === 'NUMBER') {
				if (gettype($value) !== 'integer') {
					$errNum = array(
						'code' => 400,
						'msg' => 'Property value invalid, current type: Number',
					);
					return $errNum;
				}
				continue;
			}

			if (is_object($value)) {
				$errObj = array(
					'code' => 400,
					'msg' => 'Property value invalid, current type: array/String/Number/bool',
				);
				return $errObj;
			}

			if (is_array($value)) {
				$errArr = $this->checkArray($value);
				if ($errArr['code'] === 400) {
					return $errArr;
				}
				if ($errArr['code'] === 60001) {
					$newValue = $value[$errArr['key']];
					if(strlen($newValue)>8092){
						$newValue = substr($newValue,0,8091).'$';
					}
					$value[$errArr['key']] = $newValue;
					return array(
						'code'=>60001,
						'key'=>$key,
						'value'=>$value,
						'msg' =>$errArr['msg']
					);
				}
				continue;
			}
			$errVal = $this->checkValue($value);
			if ($errVal['code'] === 400) {
				return $errVal;
			}
			if ($errVal['code'] === 60001) {
				$newValue = $value;
				if(strlen($value)>8092){
					$newValue = substr($value,0,8091).'$';
				}
				return array(
					'code'=>60001,
					'key'=>$key,
					'value'=>$newValue,
					'msg' =>$errVal['msg']
				);
			}
		}

		if ((count($property) == 0 || count($property) > 100)&& $type !=='NOLENGTH') {
			$errPro = array(
				'code' => 400,
				'msg' => 'The length of the property key-value pair needs to be 1-99!',
			);
			return $errPro;
		}

		return array(
			'code' => 200,
		);
	}
	private function checkObj($key, $value = '') {
		if (is_array($key)) {
			return $key;
		}
		return array(
			$key => $value,
		);
	}
	private function logText($msg, $str = "") {
		if ($this->is_debug()) {
			printf($msg, $str);
		}
	}
	private function _json_dumps($data) {
	    return urldecode(json_encode($this->array_urlencode($data)));
    }
    private function checkPlatform ($platform){
    	$pFormList = array('Java','python','JS','Node','PHP','WeChat','Android','iOS');
    	foreach ($pFormList as $key => $value) {
    		if(strtolower($value) == strtolower($platform)){
    			return $value;
    		}
		}
    	return $platform;
    }
    private function array_urlencode($data){
        $new_data = array();
        foreach($data as $key => $val){
            // ÕâÀïÎÒ¶Ô¼üÒ²½øÐÐÁËurlencode
           
            $new_data[urlencode($key)] = is_array($val) ? $this->array_urlencode($val) : (gettype($val)=='string'?urlencode($val):$val);
        }
        return $new_data;
    }
	private function upload($distinctId, $isLogin, $eventName, $properties, $platform, $xwhen) {
		if ($eventName == '$profile_set' || $eventName == '$profile_set_once' || $eventName == '$profile_increment' || $eventName == '$profile_unset' || $eventName == '$profile_delete'||$eventName == '$alias'||$eventName == '$profile_append') {
			$xcontext = array_merge($this->_baseProperties, $properties);
		}else{
			$xcontext = array_merge($this->_baseProperties,$this->_xcontextSuperProperties, $properties);
		}

		$errId= $this->checkKey($distinctId,'ID');
		if($errId['code'] == 400){
			throw new AnalysysAgentException($errId['msg']);
			return ;
		}

		if( $xwhen === null){
			$xwhen = $this->msectime();
		}else{
			$errTime = $this ->checkTime($xwhen);
			if($errTime['code'] == 400){
				throw new AnalysysAgentException($errTime['msg']);
				return ;
			}
			if($errTime['code'] == 201){
				$xwhen = (int) $xwhen;
			}
		}
		$xcontext['$platform'] = is_null($platform)||!is_string($platform)||!$platform?'PHP':$this->checkPlatform($platform);
		$xcontext['$is_login'] = $isLogin;
		$data = array(
			'xwho' => (string) $distinctId,
			'xwhen' => $xwhen,
			'xwhat' => $eventName,
			'appid' => $this->_appid,
			'xcontext' => $xcontext,
		);
		return $this->_consumer->send($this->_json_dumps($data),$this->_appid,$this->is_debug());
	}
	private function profile($distinctId, $isLogin, $eventName, $properties, $platform,$type=null, $xwhen =null){
		if (is_null($distinctId) || strlen($distinctId) == 0) {
			throw new AnalysysAgentException("aliasId is empty.");
		}

		if (!is_bool($isLogin)) {
			throw new AnalysysAgentException("isLogin is not boolean.");
		}

		if (count($properties) == 0) {
			throw new AnalysysAgentException("The length of the property key-value pair needs to be 1-99!");
		}

		$errPro = $this->checkProperty($properties,$type);
		if ($errPro['code'] == 400) {
			throw new AnalysysAgentException($errPro['msg']);
			return;
		}
		if($errPro['code'] == 60001){
			$key = $errPro['key'];
			$value = $errPro['value'];
			$properties[$key] = $value;
			print_r($errPro['msg']);
		}
		$this->upload($distinctId, $isLogin, $eventName, $properties, $platform, $xwhen);
	}
	public function setDebugMode($debug) {
		if ($debug === 1) {
			$this->_baseProperties['$debug'] = ANALYSYSAGENT_OPENNOSAVE_DEBUG;
		}
		if ($debug === 2) {
			$this->_baseProperties['$debug'] = ANALYSYSAGENT_OPENANDSAVE_DEBUG;
		}
	}

	public function registerSuperProperties($properties = null) {
		$err = $this->checkProperty($properties);
		if ($err['code'] === 400) {
			if ($this->is_debug() === true) {
				throw new AnalysysAgentException($err['msg']);
			}
		}
		if($err['code'] == 60001){
			$key = $err['key'];
			$value = $err['value'];
			$properties[$key] = $value;
			print_r($err['msg']);
		}

		$this->_xcontextSuperProperties = array_merge($this->_xcontextSuperProperties, $properties);

		if ($this->is_debug() === true) {
			$this->logText('\n registerSuperProperties success \n');
		}
	}
	public function unRegisterSuperProperty($key = null) {

		$err = $this->checkKey($key);
		if ($err['code'] === 400) {
			if ($this->is_debug() === true) {
				throw new AnalysysAgentException($err['msg']);
			}
		}
		if (array_key_exists($key, $this->_xcontextSuperProperties) == true) {
			unset($this->_xcontextSuperProperties[$key]);
			$this->logText("unregisterSuperProperty(%s): delete success", $key);
		} else {
			$this->logText("unregisterSuperProperty(%s): delete failed", $key);
		}
	}
	public function clearSuperProperties() {
		$this->clearXcontextSuperProperties();
		$this->logText("clearSuperProperties: clear success");
	}
	public function getSuperProperty($key) {
		$err = $this->checkKey($key);
		if ($err['code'] === 400) {
			if ($this->is_debug() === true) {
				throw new AnalysysAgentException($err['msg']);
			}
		}
		if (array_key_exists($key, $this->_xcontextSuperProperties) == true) {
			$this->logText("getSuperProperty(%s): delete success", $key);
			return $this->_xcontextSuperProperties[$key];
		} else {
			$this->logText("getSuperProperty(%s): delete failed", $key);
		}

	}
	public function getSuperProperties() {
		return $this->_xcontextSuperProperties;
	}

	public function profileSet($distinctId = null, $isLogin = false, $properties = array(), $platform = ANALYSYSAGENT_SDK_LIB, $xwhen =null) {
		$this->profile($distinctId, $isLogin, '$profile_set', $properties, $platform, null, $xwhen);
	}
	public function profileSetOnce($distinctId = null, $isLogin = false, $properties = array(), $platform = ANALYSYSAGENT_SDK_LIB, $xwhen =null) {
		$this->profile($distinctId, $isLogin, '$profile_set_once', $properties, $platform, null, $xwhen);
	}
	public function profileIncrement($distinctId = null, $isLogin = false, $properties = array(), $platform = ANALYSYSAGENT_SDK_LIB, $xwhen =null) {
		$this->profile($distinctId, $isLogin, '$profile_increment', $properties, $platform,'NUMBER', $xwhen);
	}
	public function profileAppend($distinctId = null, $isLogin = false, $properties = array(), $platform = ANALYSYSAGENT_SDK_LIB, $xwhen =null) {
		$this->profile($distinctId, $isLogin, '$profile_append', $properties, $platform, null, $xwhen);
	}
	public function profileUnset($distinctId = null, $isLogin = false, $key = null, $platform = ANALYSYSAGENT_SDK_LIB, $xwhen =null) {
		if (is_null($distinctId) || strlen($distinctId) == 0) {
			throw new AnalysysAgentException("aliasId is empty.");
		}

		if (!is_bool($isLogin)) {
			throw new AnalysysAgentException("isLogin is not boolean.");
		}

		$err = $this->checkKey($key);
		if ($err['code'] === 400) {
			if ($this->is_debug() === true) {
				throw new AnalysysAgentException($err['msg']);
			}
		}
		 $properties=array(
		 	$key=>''
		 );
		$this->upload($distinctId, $isLogin, '$profile_unset', $properties, $platform, $xwhen);
	}
	public function profileDelete($distinctId = null, $isLogin = false, $platform = ANALYSYSAGENT_SDK_LIB, $xwhen =null) {
		$properties=array();
		$this->upload($distinctId, $isLogin, '$profile_delete', $properties, $platform, $xwhen);
	}

	public function alias($aliasId, $original_id, $platform, $xwhen =null){
		if (is_null($aliasId) || strlen($aliasId) == 0) {
			throw new AnalysysAgentException("aliasId is empty.");
		}
		if (is_null($original_id) || strlen($original_id) == 0) {
			throw new AnalysysAgentException("original_id is empty.");
		}

		$errId= $this->checkKey($original_id,'ID');
		if($errId['code'] == 400){
			throw new AnalysysAgentException($errId['msg']);
			return ;
		}
		
		$properties = array(
			'$original_id'=>$original_id
		);

		$this->upload($aliasId, true, '$alias', $properties, $platform, $xwhen);
	}
	public function track($distinctId = null, $isLogin = false,$eventName=null, $properties = array(), $platform = ANALYSYSAGENT_SDK_LIB, $xwhen =null) {
		if (is_null($distinctId) || strlen($distinctId) == 0) {
			throw new AnalysysAgentException("aliasId is empty.");
		}
		if (!is_bool($isLogin)) {
			throw new AnalysysAgentException("isLogin is not boolean.");
		}

		$errName = $this->checkkey($eventName,'NAME');
		if ($errName['code'] == 400) {
			throw new AnalysysAgentException($errName['msg']);
			return;
		}

		$errPro = $this->checkProperty($properties,'NOLENGTH');
		if ($errPro['code'] == 400) {
			throw new AnalysysAgentException($errPro['msg']);
			return;
		}
		if($errPro['code'] == 60001){
			$key = $errPro['key'];
			$value = $errPro['value'];
			$properties[$key] = $value;
			print_r($errPro['msg']);
		}
		$this->upload($distinctId, $isLogin, $eventName, $properties, $platform, $xwhen);
	}
	public function flush() {
        $this->_consumer->flush();
    }
    public function close() {
        $this->_consumer->close();
    }
}

abstract class Consumer {
	public abstract function send($data,$appid,$debug);

	public function upload() {}

	public function flush() {}

	public function close() {}
}

class BatchConsumer extends Consumer {
	private $_buffers;
	private $_max_data_size;
	private $_server;
	private $_timeout;
	private $_appid;
	private $_debug;

	private function logText($msg) {
		if ($this->_debug == true) {
			print_r($msg);
		}
	}

	public function __construct($server, $max_data_size = 20, $timeout = 10) {
		$parsed_url = parse_url($server);
		if ($parsed_url === false) {
			throw new AnalysysAgentException("server url invalid.");
		}

		if (substr($server, -1) !== '/') {
			$server = $server . '/';
		}
		$server = $server . 'up';
		$this->_buffers = array();
		$this->_max_data_size = $max_data_size;
		$this->_server = $server;
		$this->_timeout = $timeout*1000;
	}
	public function send($msg,$appid,$debug) {
		$this->_appid = $appid;
		$this->_debug = $debug;
		$this->_buffers[] = $msg;
		if (!empty($this->_buffers)&&count($this->_buffers) >= $this->_max_data_size) {
			return $this->flush();
		}
		return true;
	}
	public function flush() {
		if (empty($this->_buffers)) {
			$ret = false;
		} else {
			$this->logText($this->_buffers);
			$ret = $this->_do_request($this->_encode_msg_list($this->_buffers));
		}
		if ($ret) {
			$this->_buffers = array();
		}
		return $ret;
	}

	protected function _do_request($data) {
		$ch = curl_init();
		$rq_server = $this->_server . '?appid=' . $this->_appid;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $rq_server);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->_timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->_timeout);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_USERAGENT, "ANALYSYSAGENT PHP SDK");
		$pos = strpos($rq_server, "https");
		if ($pos === 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		$ret = curl_exec($ch);
		if (false === $ret) {
			curl_close($ch);
			return false;
		} else {
			curl_close($ch);
			return true;
		}
	}

	private function _encode_msg_list($msg_list) {
		return base64_encode($this->_gzip_string("[" . implode(",", $msg_list) . "]"));
	}

	private function _gzip_string($data) {
		return gzencode($data);
	}
	public function close() {
		return $this->flush();
	}
}

class SyncConsumer extends Consumer {
	private $_buffers;
	private $_server;
	private $_appid;
	private $_debug;

	private function logText($msg) {
		if ($this->_debug == true) {
			print_r($msg);
		}
	}

	public function __construct($server) {
		$parsed_url = parse_url($server);
		if ($parsed_url === false) {
			throw new AnalysysAgentException("server url invalid.");
		}

		if (substr($server, -1) !== '/') {
			$server = $server . '/';
		}
		$server = $server . 'up';
		$this->_buffers = array();
		$this->_server = $server;
	}
	public function send($msg,$appid,$debug) {
		$this->_debug=$debug;
		$this->_appid = $appid;
		$this->_buffers[] = $msg;
		if (count($this->_buffers)>0) {
			return $this->flush();
		}
	}
	public function flush() {
		if (empty($this->_buffers)) {
			$ret = false;
		} else {
			$this->logText($this->_buffers);
			$this->_do_request($this->_encode_msg_list($this->_buffers));
			$ret = true;
		}
		if ($ret) {
			$this->_buffers = array();
		}
		return $ret;
	}

	protected function _do_request($data) {
		$ch = curl_init();
		$rq_server = $this->_server . '?appid=' . $this->_appid;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $rq_server);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 1*1000);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1*1000);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_USERAGENT, "ANALYSYSAGENT PHP SDK");
		
		$pos = strpos($rq_server, "https");
		if ($pos === 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		$ret = curl_exec($ch);
		if (false === $ret) {
			curl_close($ch);
			return false;
		} else {
			curl_close($ch);
			return true;
		}
	}

	private function _encode_msg_list($msg_list) {
		return base64_encode($this->_gzip_string("[" . implode(",", $msg_list) . "]"));
	}

	private function _gzip_string($data) {
		return gzencode($data);
	}
	public function close() {
		return $this->flush();
	}
}
