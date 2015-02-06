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
use Ivy\core\CException;
class IndexController extends AuthController {
	/**
     * 本控制器的权限认证
     * @return [type] [description]
     */
    public function actionBefore(){
        $result = \Ivy::app()->user->checkAccess($this->route);
        $result = true;

        if($result==false){
            $route = implode('->', $this->route->getRouter());
            throw new CException('没有授权该操作！'.$route);
        }
    }

	/**
	 * 自动权限管理检测入口
	 */
	public function indexAction() {
		
		var_dump($this);
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
