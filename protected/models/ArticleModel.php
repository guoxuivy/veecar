<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
use \Ivy\core\CException;
class ArticleModel extends \CActiveRecord
{

	//设置附件上传目录

    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'article';
	}
	public function init()
	{
	}

	/**
	 * 附件零时保存目录
	 * @return [type] [description]
	 */
	static function getUploadTmp(){
		return ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload" . DIRECTORY_SEPARATOR;
	}
	/**
	 * 文章附件保存目录
	 * @return [type] [description]
	 */
	static function getUploadDir(){
		$targetDir = __ROOT__.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR."article".DIRECTORY_SEPARATOR.date("Ymd").DIRECTORY_SEPARATOR;
		if (!file_exists($targetDir)) {
			@mkdir($targetDir,0777,true);
		}
		return $targetDir;
	}

	/**
	 * 添加一条新记录
	 * @param array $post [description]
	 */
	public function saveOne($post=array()){
		if(isset($post['available_from'])&&empty($post['available_from']))
			unset($post['available_from']);
		if(isset($post['available_to'])&&empty($post['available_to']))
			unset($post['available_to']);
		if(isset($post['status'])&&empty($post['status']))
		$this->attributes=$post;
		try{
			//开启事务处理  
	        $this->db->beginT();
	        $this->save();
	    	$tmp_images=array();
	        if(isset($post['tmp_images'])){
	        	foreach ($post['tmp_images'] as $value) {
	        		list($name,$target_name)=explode("|", $value);
	        		$ext = @pathinfo($name, PATHINFO_EXTENSION);
	        		$uri = 'article/'.date("Ymd").'/'.$target_name;
	        		$sql = "INSERT INTO `attachment` (`rel_id`, `table`, `name`, `uri`, `ext`) VALUES ( {$this->id}, 'article','".$name."', '".$uri."', '".$ext."')";
	        		$this->db->exec($sql);
	        		$tmp_images[]=array("t"=>$target_name,'n'=>$name);
	        	}
	        }
	        $this->db->commitT();
	    }catch(CException $e){
	        $this->_error[]=$e->getMessage();
	        $this->db->rollbackT();
	    }

		//物理文件保存
		if(empty($this->_error)){
			foreach ($tmp_images as $v) {
				if(!@rename(self::getUploadTmp().$v['t'], self::getUploadDir().$v['t'])) {
		            $this->_error[] = '文件('.$v['n'].')上传保存错误！请从新上传文件！';
		        }
			}
		}
		//物理文件保存
		if(empty($this->_error)){
			return true;
		}
		return false;
	}


	/**
	 * 删除一条新记录
	 * @param array $post [description]
	 */
	public function delOne($id){
		try{
			//开启事务处理  
	        $this->db->beginT();
	        $article = $this->findByPk($id)->delete();

	        //图片附件删除
	        \AttachmentModel::model()->delByRel($id,'article');


	        $this->db->commitT();
	    }catch(CException $e){
	        $this->_error[]=$e->getMessage();
	        $this->db->rollbackT();
	    }

		if(empty($this->_error)){
			return true;
		}
		return false;
	}




	
}