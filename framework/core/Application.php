<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license https://github.com/guoxuivy/ivy/
 * @package framework
 * @link https://github.com/guoxuivy/ivy 
 * @since 1.0 
 */
namespace Ivy\core;
use \Ivy\cache\AbsoluteCache;
use \Ivy\db\AbsoluteDB;
final class Application extends CComponent {
	/**
	 * 数据库实例句柄
	 */
	protected $db = NULL;
	/**
	 * 登录用户
	 */
	protected $user = NULL;
	/**
	 * cache
	 */
	protected $cache = NULL;
	/**
	 * 全局配置文件
	 */
	protected $config = NULL;
	/**
	 * 当前路由副本保存，共loaderClass使用
	 */
	protected $temp_route = NULL;


	/**
	 * 加载全局配置文件
	 */
	public function __construct($config){
		\Ivy::setApplication($this);//保存句柄
		$config = require_once($config);
		$this->config=$config;
	}

	/**
	 * 数据库句柄对象
	 * 同一时刻仅支持单一数据库连接
	 */
	public function getDb() {
		if($this->db instanceof AbsoluteDB){
			return $this->db;
		}else{
			$this->db = AbsoluteDB::getInstance($this->config['db_pdo']);
			return $this->db;
		}
	}

	/**
	 * 缓存句柄对象
     * 同一时刻仅支持单一缓存方式
	 */
	public function getCache() {
		if($this->cache instanceof AbsoluteCache){
			return $this->cache;
		}else{
			$this->cache = AbsoluteCache::getInstance($this->config['memcache']);
			return $this->cache;
		}
	}

	/**
	 * 登录用户句柄对象
	 */
	public function getUser() {
		if($this->user instanceof User){
			return $this->user;
		}else{
			$this->user = new User();
			return $this->user;
		}
	}

	/**
	 * widget 与 run 类似直接输出 不返回
	 * @comment 自动适配分组模式 优先适配普通模式的控制器
	 * @param $routerStr  路由参数  
	 * @param $param array 自定义参数
	 */
	public function widget($routerStr,$param=array()) {
		$route = new Route();
		if(is_array($routerStr)) $routerStr=implode("/",$routerStr);
		$route->start($routerStr);
		return $this->dispatch($route,$param);
	}

	/**
	 * 执行路由
	 * 直接输出结果 无返回值
	 * @return null
	 */
	public function run() {
		$route = new Route();
		$routerStr=isset($_GET['r'])?$_GET['r']:"";
		$route->start($routerStr);
        $param=$_GET;
        unset($param['r']);
		$this->dispatch($route,$param);
		$this->finished();
	}

	/**
	 * 分发 
	 * @param $routerObj obj 路由对象
	 * @param $param array 附带参数数组 
	 * @comment 自动适配分组模式 优先适配普通模式的控制器
	 * 
	 * 'module' => 'admin'          //分组（非必须）
	 * 'controller' => 'roder'      //控制器（必须）
	 * 'action' => 'index'          //方法（必须）
	 */
	public function dispatch($routerObj,$param=array()) {
		$router=$this->temp_route=$routerObj->getRouter();
		$module=isset($router['module'])?strtolower($router['module']):"";
		$class=ucfirst(strtolower($router['controller']))."Controller";
		$action=strtolower($router['action']).'Action';
		if(''==$module){
			try{
				$ReflectedClass = new \ReflectionClass($class); // 2级控制器检测 非分组模式
			}catch(CException $e){
				//试图适配分组模式
				$routerObj->setRouter(array('module'=>$router['controller'],'controller'=>$router['action']));
				return $this->dispatch($routerObj,$param);
			}
		}
		try{
			//两级或者三级 2级则必定存在此控制器，3级则不一定
			if(''!==$module) $class=$module."\\".$class; 
			$ReflectedClass = new \ReflectionClass($class);
		}catch(CException $e){
			throw new CException ( $router['module'].'/'.$router['controller'] . '-不存在！'); 
		}
		
		$controller_obj = $ReflectedClass->newInstanceArgs(array($routerObj));
		if($ReflectedClass->hasMethod("actionBefore")){
			 $controller_obj->actionBefore();
		}
        $_before=str_replace('Action','Before',$action);
        if($ReflectedClass->hasMethod($_before)){
            $this->_doMethod($controller_obj, $_before, $param);
		}
        $result = $this->_doMethod($controller_obj, $action, $param);
        $_after=str_replace('Action','After',$action);
        if($ReflectedClass->hasMethod($_after)){
            $this->_doMethod($controller_obj, $_after, $param);
		}
		if($ReflectedClass->hasMethod("actionAfter")){
			 $controller_obj->actionAfter();
		}
		return $result;
	}
    
    
    /**
    * 自动适配参数 并且执行
    * @param string $method
    * @param array $args
    * @return mixed
    */
    private function _doMethod($obj, $method, array $args = array())
    {
        $reflection = new \ReflectionMethod($obj, $method);
        $pass = array();
        foreach($reflection->getParameters() as $param)
        {
            if(isset($args[$param->getName()]))
            {
                $pass[] = $args[$param->getName()];
            }else{
                try{
                    $pass[] = $param->getDefaultValue();
                }catch(\ReflectionException $e){
                    $pass[] = null;
                }
            }
        }
        return $reflection->invokeArgs($obj, $pass);
    }

	/**
	 * 后去runtime路径
	 * @return string
	 */
	public function getRuntimePath() {
		return __PROTECTED__.DIRECTORY_SEPARATOR.'runtime';
	}

	/**
	 * 正常结束处理
	 * @return [type] [description]
	 */
	public function finished() {
	}
	
}