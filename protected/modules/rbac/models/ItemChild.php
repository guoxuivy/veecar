<?php
namespace rbac;
use \Ivy\core\CException;
class ItemChild extends \CActiveRecord
{
    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'auth_item_child';
	}

    
}