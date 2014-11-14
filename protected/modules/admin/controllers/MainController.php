<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
namespace admin; 
class MainController extends \CController {
    /**
	 * head载入
	 */
	public function headAction() {
        $this->view->assign('page_title','V-CAR 微改装网')->display();
	}
    
    /**
	 * end body载入
	 */
	public function footAction() {
        $this->view->assign()->display();
	}
    
    
    /**
	 * end body载入
	 */
	public function topnavAction() {
        $this->view->assign()->display();
	}
    
    /**
	 * SIDEBAR MENU载入
	 */
	public function menuAction() {
        $this->view->assign(array(
            'menu_active'=>$_REQUEST['nav']
        ))->display();
	}
	
}
