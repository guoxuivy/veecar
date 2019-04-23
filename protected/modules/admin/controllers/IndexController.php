<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
namespace admin;
use Ivy\core\lib\UploadFile;
class IndexController extends \SController {
    
    /**
	 * 显示模版实例示例
	 */
	public function indexAction() {
        $this->view->assign()->display();
	}


    /**
     * [文件上传操作]
     */
	public function UploadAction(){
    	if ($this->isPost) {
    		//$this->checkToken();
    		//$data = $_POST['info'];
    		//$data['published'] = time();
    		
    		//import('ORG.Net.UploadFile');
    		$upload = new UploadFile();// 实例化上传类
    		$upload->maxSize  = 3145728 ;// 设置附件上传大小
    		$upload->saveRule  = date('Ymj').'_p'.time();// 设置文件名
    		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    		$upload->savePath =  __ROOT__.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;// 设置附件上传目录
    		$upload->thumb = true; 
    		//设置需要生成缩略图的文件后缀 
    		$upload->thumbPrefix = 'm_';  //生产1张缩略图 
    		//设置缩略图最大宽度 
    		$upload->thumbMaxWidth = '220'; 
    		//设置缩略图最大高度 
    		$upload->thumbMaxHeight = '220'; 
    		
    		if(!$upload->upload()) {
    			\Ivy::log($upload->getErrorMsg()); // 上传错误提示错误信息
    			//$this->error($upload->getErrorMsg());// 上传错误提示错误信息
    		}else{
    			$info =  $upload->getUploadFileInfo();// 上传成功 获取上传文件信息
    		}
    		\Ivy::log('上次成功');
    		
    		die('上次成功');
    	}else {
    		//$this->display();
    	}
    }
	
}
