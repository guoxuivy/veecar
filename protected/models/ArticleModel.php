<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
use \Ivy\core\CException;
class ArticleModel extends \CModel
{

	//设置附件上传目录

    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'article';
	}
	/**
	 * 附件零时保存目录
	 * @return [type] [description]
	 */
	static function getUploadTmp(){
		return ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload" . DIRECTORY_SEPARATOR;
	}
	/**
	 * 附件保存目录
	 * @return [type] [description]
	 */
	static function getUploadDir(){
		return __ROOT__.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
	}

	/**
	 * 添加一条新记录
	 * @param array $post [description]
	 */
	public function addOne($post=array()){
		if(isset($post['available_from'])&&empty($post['available_from']))
			unset($post['available_from']);
		if(isset($post['available_to'])&&empty($post['available_to']))
			unset($post['available_to']);

		$this->attributes=$post;
		try{
			//开启事务处理  
	        $this->db->beginT();
	        $_pk = $this->save();
	    	$tmp_images=array();
	        if(isset($post['tmp_images'])){
	        	foreach ($post['tmp_images'] as $value) {
	        		list($name,$target_name)=explode("|", $value);
	        		$ext = @pathinfo($name, PATHINFO_EXTENSION);
	        		$sql="INSERT INTO `attachment` (`rel_id`, `table`, `name`, `uri`, `ext`) VALUES ( {$_pk}, 'article','".$name."', '".$target_name."', '".$ext."')";
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

	
}