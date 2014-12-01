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
class SController extends CController
{
    /**
     * 用户登录验证 
     **/
    public function init() {
        if(\Ivy::app()->user->isGuest){
            //记录返回地址
            \Ivy::app()->user->setReturnUrl(implode('/',$this->route->getRouter()));
            $this->redirect('admin/main/login');
        }
	}

}