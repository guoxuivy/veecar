<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
namespace admin; 
class NavModel extends \CModel
{
    /**
     * 必须方法
     **/
    public function tableName()
	{
		return 'admin_nav';
	}
	
}