<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
use \Ivy\core\Controller;
class ErrorController extends Controller {
	
	//布局文件
	public $layout=null;

	public function indexAction() {
		$code=$_REQUEST['code']?$_REQUEST['code']:'404';
		$msg=$_REQUEST['msg']?$_REQUEST['msg']:'哎哟...  您访问的页面不存在';
		$this->view->assign(array('code'=>$code,'msg'=>$msg))->display();
	}
    
}
