<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
use \Ivy\core\CException;
class AttachmentModel extends \CModel
{

	//设置附件上传目录

    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'attachment';
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
	 * 删除一条新记录 删除附件
	 * @param array $post [description]
	 */
	public function delByRel($id,$table){
		$attas = $this->db->findAllBySql(" select * from `attachment` where `table` = '{$table}' and `rel_id` = {$id} ");
        if($attas){
        	foreach ($attas as $atta) {
        		$a_model = new \AttachmentModel();
        		$a_model->attributes = $atta;
        		$a_model->delete();
        		unlink("./upload/".$atta['uri']);
        	}
        }
        return true;
	}


	/**
	 * 删除一条新记录
	 * @param array $post [description]
	 */
	public function delOne($id){
		

		
	}




	
}