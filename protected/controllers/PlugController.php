<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
class PlugController extends \CController {
	private static  $_PLUGDIR = null;

	 
	public function init() {
		self::$_PLUGDIR = __PROTECTED__.DIRECTORY_SEPARATOR."plugs";
	}
   	/**
	 * 百度编辑器 插件服务端
	 */
	public function ueditorAction() {
		$contro = self::$_PLUGDIR.DIRECTORY_SEPARATOR."ueditor".DIRECTORY_SEPARATOR."controller.php";
		require_once($contro);
	}

	
    
    
	
}