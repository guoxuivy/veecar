<?php
/**
 * 权限管理 扩展 前端逻辑控制器
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 * @see 
 * 
 */
namespace rbac;
class IndexController extends \CController {
	protected $auth_route = null;		//当前正在自动验证的方法

	/**
	 * 自动权限管理检测入口
	 */
	public function indexAction() {
		
        $this->view->assign()->display();
	}
	
	public function addItemAction() {
		
		$model = new AuthItem;
		$model->attributes=$_POST;
		$model->save();
		var_dump($model);
		$this->view->assign()->display('index');
	}
}
