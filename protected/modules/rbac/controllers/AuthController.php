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
class AuthController extends \CController {
	protected $auth_route = null;		//当前正在自动验证的方法
    
    /**
	 * 权限管理处理逻辑
	 */
	public function checkAction() {
		$this->auth_route = $_REQUEST['route'];
		if (!isset(\Ivy::app()->user->authorized)) {
		 	\Ivy::app()->user->authorized = $this;
		}

		
		//var_dump(\Ivy::app());die;
        //$this->view->assign()->display();
	}

	/**
	 * 权限检测
	 * @return boolen
	 */
	protected function checkAccess(){

	}
	
}
