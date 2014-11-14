<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
namespace Ivy\core;
use Ivy\core\lib\FlexiHash;
class Cache {
    private $_memcache = array();
    //一致性哈希对象
    private $_FlexiHash = null;
    
    public function __construct($config){
        try {
			$FlexiHash = new FlexiHash($config);
            $this->_FlexiHash = $FlexiHash;
		} catch ( CException $e ) {
			throw new CException ( $e->getMessage() );
		}
    }
    /**
     * 获取对应的memcache服务器 连接句柄
     **/
    private function _connectMemcache($key){
        $config = $this->_FlexiHash->get($key);
        if (!isset($this->_memcache[$config])){
            
            $this->_memcache[$config] = new \Memcache;  
            list($host, $port) = explode(":", $config);
            $res = $this->_memcache[$config]->connect($host, $port); 
            if(!$res){
                throw new CException("memcache->".$host.":".$port.' off');
            }
            $this->_memcache[$config]->connect($host, $port); 
        }
        return $this->_memcache[$config];  
    }
    /**
     * 测试探针 获取key对应的物理节点
     **/
    public function getConfigByKey($key){
        return $config = $this->_FlexiHash->get($key);
    } 
	
    public function set($key, $value, $expire=0){
        return $this->_connectMemcache($key)->set($key, json_encode($value), 0, $expire);  
    }  
    
    public function get($key){  
        return $this->_connectMemcache($key)->get($key, true);  
    } 
    
    public function add($key, $vakue, $expire=0){  
        return $this->_connectMemcache($key)->add($key, json_encode($value), 0, $expire);  
    }   
    
    public function delete($key){  
        return $this->_connectMemcache($key)->delete($key);  
    } 
}