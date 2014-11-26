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
        $this->view->assign('page_title','V-CAR 微改装 车生活')->display();
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
	   
        $nav = NavModel::model()->findAll('type = 1');
        
        $navTree = $this->treeNavData($nav);
        if(isset($_REQUEST['active'])){
            $way = $this->getTreeWay($navTree,$_REQUEST['active']);
        }
        $this->view->assign(array(
            'way'=>isset($way)?$way:false,
            'nav_arr'=>$navTree
        ))->display('sidebar');
	}
    
    /**
	 * 节点转为数结构
     * $arr 节点树 
     * $sys_name 要查找的节点
	 */
    public function treeNavData($arr,$pid=0){
        $ret = array();
        foreach($arr as $k => $v) {
            if($v['fid'] == $pid) {
                $tmp = $arr[$k];unset($arr[$k]);
                $tmp['children'] = $this->treeNavData($arr,$v['id']);
                $ret[] = $tmp;
            }
        }
        return $ret;
    }
    /**
	 * 获取节点路径
     * $arr 节点树 
     * $id 要查找的节点sys_name
	 */
     public function getTreeWay($arr,$search){
        static $ret = array();
        static $find = false; 
        foreach($arr as $k =>$v) { 
            if($find) break;
            $ret[]=$v['id'];
            if($v['sys_name'] == $search){
                $find=true;
            }
            if(!empty($v['children'])){
                $this->getTreeWay($v['children'],$search);
            }
            if(!$find) array_pop($ret);
        }
        return $ret;   
    }

    
    /**
	 * 登录
	 */
	public function loginAction() {
        if($this->isPost){
            $nav = UserModel::model()->findByPk('1');
            var_dump($nav['nickname'],$nav->nickname);die;
        }else{
            $this->view->assign()->display();
        }
	}
     
    
	
}
