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
class ArticleController extends \SController {
    
    /**
	 * 显示文章列表
	 */
	public function listAction() {
        $this->view->assign()->display();
	}

    
    public function saveAction(){
        $res = \ArticleModel::model()->addOne($_POST['product']);
        if($res){
            $this->redirect('admin/article/list');
        }else{
            var_dump(\ArticleModel::model()->_error);die;
        }
    }


    /**
     * json文章列表
     * {"data":[],"draw":0,"recordsTotal":2430,"recordsFiltered":2430}
     * @return  json [description]
     */
    public function jsonAction() {
        $star=$_REQUEST['start']?$_REQUEST['start']:0;
        $length=$_REQUEST['length']?$_REQUEST['length']:10;
        $page = (int)ceil($star/$length+1);

        $list = \ArticleModel::model()->getPagener(NULL,$page,$length);
        $data=array();
        foreach ($list['list'] as $value) {
            $data[]=array(
                '<input value="'.$value['id'].'" type="checkbox">',
                $value['id'],
                $value['title'],
                $value['cates'],
                date('Y-m-d H:i',$value['add_time']),
                $value['summary'],
                $value['available_from'],
                $value['status'],
                '无'
            );
        }
        $recordsTotal=$list['recordsTotal'];
        die(json_encode(array('data'=>$data,'recordsTotal'=>$recordsTotal))); 
    }


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
