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
use Ivy\core\CException;
class ArticleController extends \SController {
    public $layout=null;
    /**
	 * 显示文章列表
	 */
	public function listAction() {
        if($this->isAjax)
            $this->jsonList();
        $this->view->assign()->display();
	}

    /**
     * json文章列表 recordsFiltered 必须！！
     * {"data":[],"draw":0,"recordsTotal":2430,"recordsFiltered":2430}
     * @return  json [description]
     */
    protected function jsonList() {
        $star=$this->request->param('start',0);
        $length=$this->request->param('length',10);
        $page = (int)ceil($star/$length+1);

        $order=$this->request->param('order/a');
        $sort = [];
        if($order){
            $columns=$this->request->param('columns/a');
            foreach ($order as $s){
                $key = $columns[$s['column']]['data'];
                $sort[$key] = $s['dir'];
            }
        }
        $list = \ArticleModel::model()->page($page,$length)->order($sort)->getPagener();
        $data=array();
        foreach ($list['list'] as $value) {
            $data[]=array(
                'id'=>$value['id'],
                'title'=>$value['title'],
                'cates'=>$value['cates'],
                'add_time'=>date('Y-m-d H:i',$value['add_time']),
                'summary'=>$value['summary'],
                'available_from'=>$value['available_from'],
                'status'=>$value['status']
            );
        }
        $recordsTotal=$list['pagener']['recordsTotal'];
        die(json_encode(array('data'=>$data,'recordsTotal'=>$recordsTotal,"recordsFiltered"=>$recordsTotal))); 
    }

    public function addAction() {
        $this->view->assign()->display('edit');
    }

    public function editAction(){

        //\LogModel::model()->test();
        $id=$_REQUEST['id'];
        $data = \ArticleModel::model()->findByPk($id);
        $imgs = \AttachmentModel::model()->where("`rel_id` = {$id} and `table`='article'")->findAll();
        $this->view->assign(array('data'=>$data,'imgs'=>$imgs))->display();
    }

    public function deleteAction(){
        $id=(int)$_REQUEST['id'];
        $res = \ArticleModel::model()->delOne($id);
        if($res){
            $this->redirect('admin/article/list');
        }else{
            throw new CException(\ArticleModel::model()->_error[0]);
        }
    }
    
    public function saveAction(){
        $res = \ArticleModel::model()->saveOne($_POST['product']);
        if($res){
            $this->redirect('admin/article/list');
        }else{
            throw new CException(\ArticleModel::model()->_error[0]);
        }
    }


    












/**
 * 传统 file post上传测试
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
