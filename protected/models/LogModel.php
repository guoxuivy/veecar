<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
use \Ivy\core\CException;
class LogModel extends \CActiveRecord
{

	//设置附件上传目录

    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'be_log';
	}


	/**
	 * 性能测试
	 * @param array $post [description]
	 */
	public function test(){
		//$result=$this->db->findBySql("explain select * from be_log where log_id <100  limit 3 ");
		//var_dump($result);die;

		$star = xdebug_time_index(); 
        for ($i = 1; $i < 1000; $i++)  
        {  
            $result=$this->findByPk($i);
        }
        $end = xdebug_time_index(); 
        echo $end-$star, "\n";
        die;

	}




	
}