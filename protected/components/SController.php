<?php
/**
 * 后台基类 需要登录验证
 * Ivy class file.
 * All controller classes for this application should extend from this base class.
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
use Ivy\core\CException;
use rbac\AuthController;
class SController extends AuthController
{
    /**
     * 用户登录验证 
     **/
    public function init() {
        if(\Ivy::app()->user->isGuest){
            //记录返回地址
            \Ivy::app()->user->setReturnUrl(implode('/',$this->route->getRouter()).$this->getQueryString());
            $this->redirect('admin/main/login');
        }
	}

    public function __call($method, $param){
        if("Action"===substr($method,-6)) {
            $view=substr($method,0,-6);
            $this->view->assign()->display($view);
        }else{
            throw new CException('找不到'.$method.'方法');
        }
        
    }


    /**
     * 获取 r之外的 get参数 
     * @return [string] [description]
     */
    public function getQueryString(){
        $q = explode("&", $_SERVER['QUERY_STRING']);
        if($q && count($q)>1 && $q[0][0]==='r'){
            unset($q[0]);
            return "&".implode('&', $q);
        }
        return '';
    }

}