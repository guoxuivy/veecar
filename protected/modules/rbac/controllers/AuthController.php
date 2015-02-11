<?php
/**
 * 权限管理 扩展 
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 * @see 
 * 
 */
namespace rbac;
use Ivy\core\Controller;
use Ivy\core\CException;
class AuthController extends Controller {

	protected $_rbac_confit=null; 	//rbac配置

	public function __construct($route=NULL){
		$confit=\Ivy::app()->config;
		if(!isset($confit['rbac'])) 
			throw new CException('rbac 配置错误！');
        $this->_rbac_confit=$confit['rbac'];
        parent::__construct($route);
    }



    /**
     * 权限控制处理 验证权限 交由 user 处理 方便缓存 登出
     * @return [type] [description]
     */
    public function actionBefore(){
        $result = \Ivy::app()->user->checkAccess($this->route);
        if($result==false){
            $route = implode('->', $this->route->getRouter());
            throw new CException('没有授权该操作！'.$route);
        }
    }

	/**
	 * 权限检测  是否持有权限
	 * @return boolen
	 */
	public function checkAccess($route=null,&$auth_list=null){
		if($route==null || $auth_list==null) return false;
		$acc="";
		$route_arr=$route->getRouter();
		if(isset($route_arr['module'])&&$route_arr['module']!=null) $acc.=strtolower($route_arr['module'])."@";
		if(isset($route_arr['controller'])&&$route_arr['controller']!=null) $acc.=strtolower($route_arr['controller'])."#";
		if(isset($route_arr['action'])&&$route_arr['action']!=null) $acc.=strtolower($route_arr['action']);

		return in_array($acc,$auth_list);

	}


	/**
	 * 获取用户 权限数组 对外提供 
	 * @param  int $userId [description]
	 * @return array
	 */
	public function getAuthList($userId=null){
		if($userId==null){
			$id_str = $this->_rbac_confit["userid"];
			$userId==\Ivy::app()->user->$id_str;
		}
		$list = $this->getMethodListByUId($userId);
		return $list;
	}

	

	/**
	 * 获取用户任务列表
	 * @param  int $userId [description]
	 * @return array
	 */
	protected function getTaskListByUId($userId){
		$task_list=array();
		//拥有角色列表
		$role_list=Assignment::model()->findAll("userid=".$userId);
		foreach ($role_list as $value) {
			$_list=$this->getChild($value['itemname']);
			$task_list = array_merge($task_list,$_list);
		}
		return $task_list;
	}

	/**
	 * 获取用户操作列表
	 * @param  int $userId [description]
	 * @return array
	 */
	protected function getMethodListByUId($userId){
		$method_list=array();
		$task_list=$this->getTaskListByUId($userId);
		foreach ($task_list as $value) {
			$_list=$this->getChild($value);
			$method_list = array_merge($method_list,$_list);
		}
		return $method_list;
	}

	/**
	 * 获取子节点数组
	 * @param  parent
	 * @return array 
	 */
	protected function getChild($parent){
		$child_list=array();
		$_list=ItemChild::model()->findAll("parent='{$parent}'");
		foreach ($_list as $value) {
			$child_list[] = $value['child'];
		}
		return $child_list;
	}

}
