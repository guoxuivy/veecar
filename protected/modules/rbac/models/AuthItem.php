<?php
namespace rbac;
use \Ivy\core\CException;
class AuthItem extends \CActiveRecord
{
    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'auth_item';
	}

	

    
}