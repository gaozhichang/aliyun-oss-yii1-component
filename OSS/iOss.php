<?php
#namespace OSS;
use \OSS\OssClient;
use \OSS\Core\OssException;
#require_once 'D:/zend/coding2/itzlib/plugins/OSS/OssClient.php';
class iOss extends  \CApplicationComponent
{  	
  	public $accessKeyId='';//访问密钥
  	public $accessKeySecret='';//访问密钥
  	public $endpoint='';//访问域名
  	public $bucket='';//存储空间
  	private $_oss;//实例对象
  	
  	/**
  	 * 根据Config配置，得到一个OssClient实例
  	 *
  	 * @return OssClient 一个OssClient实例
  	 */
	public function init() {
		parent::init();
		
		//register autoloader
		spl_autoload_register(array("iOss","classLoader"));
		
		try {
			$this->_oss = new OSS\OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, false);
		} catch (OssException $e) {
			Yii::log("creating OssClient instance: FAILED. ".$e->getMessage(),CLogger::LEVEL_ERROR);
		}
		return $this->_oss;
	}
	
	//直接Yii::app()->oss->listBuckets() 方式调用。映射到oss客户端
	public function __call($method, $args){
		if (isset($this->_oss) && method_exists($this->_oss, $method)){
			return call_user_func_array(array($this->_oss, $method), $args);
		}else{
			Yii::log(get_class($this->_oss)," object not found method: ".$method,CLogger::LEVEL_ERROR);
			return false;
		}
	}
	
	//Yii::app()->getClient()->listBuckets() 方式调用
	public function getClient(){
		if(!$this->_oss){
			$this->init();
		}
		return $this->_oss;
	}
	
	
	private function classLoader($class)
	{	
		$path = str_replace('OSS\\', DIRECTORY_SEPARATOR, $class);
		$file = __DIR__ . DIRECTORY_SEPARATOR .$path . '.php';
		if (file_exists($file)) {
			require_once $file;
		}
	}
}


