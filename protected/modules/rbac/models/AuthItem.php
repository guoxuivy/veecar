<?php
namespace rbac;
use \Ivy\core\CException;
class AuthItem extends \CActiveRecord
{

	/**
	 * 所有字符串必须小写
	 */

    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'auth_item';
	}


	/**
	 * 批量收录操作
	 * @param array $list 方法名数组
	 * @return  boolen
	 */
	public function addMethods($list){
		$sql = 'insert into `'.$this->tableName().'` (`name`,`type`) values ';
		foreach ($list as $v) {
			$sql.= "( '{$v}' , 0 ),";
		}
		$sql=substr($sql,0,-1);
		return $this->exec($sql);
	}
}