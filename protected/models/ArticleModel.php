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
	//protected static $_img_dir = __ROOT__.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;

    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'article';
	}

	/**
	 * 添加一条新记录
	 * @param array $post [description]
	 */
	public function addOne($post=array()){
		$this->attributes=$post;
		try{
			//开启事务处理  
	        $res = $this->db->beginT();
	        $res = $this->save();
	        if(!$res) 
	        	throw new CException("article表保存失败");
	        if(isset($post['tmp_images'])){
	        	foreach ($post['tmp_images'] as $value) {
	        		list($name,$target_name)=explode("|", $value);
	        		$sql="INSERT INTO `attachment` (`name`, `uri`) VALUES ('".$name."', '".$target_name."')";
	        		$this->db->exec($sql);
	        	}
	        }
	        $this->db->commitT();
	    }catch(CException $e){
	        $this->_error[]=$e->getMessage();
	        $this->db->rollbackT();
	    }catch(\PDOException $e){
	        $this->db->rollbackT();
			$this->_error[]=$e->getMessage();
		}
		//物理文件保存
		if(empty($this->_error)){

		}
	}
	
}