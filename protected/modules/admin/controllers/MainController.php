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
	   
        $nav = NavModel::model()->findAll('type = 1');
        if(isset($_REQUEST['active'])){
            //$find_id = NavModel::model()->find("sys_name = '{$_REQUEST['active']}'")->id;
            $find_id = $this->getSysId($nav,$_REQUEST['active']);
            $way = $this->getTreeWay($nav,$find_id);
        }
        $navTree = $this->treeNavData($nav);
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
     * $arr 节点数组 
     * $id 要查找的节点id
	 */
     public function getTreeWay($arr,$id){
        static $ret = array();
        foreach($arr as $k =>$v) { 
            if($v['id'] == $id){
                $ret[]=$v['id'];
                $this->getTreeWay($arr,$v['fid']);
            }
        }
        return $ret;   
    }
    /**
	 * 查找节点id
     * $arr 节点数组 
     * $id 要查找的节点sys_name
	 */
     public function getSysId($arr,$sys_name){
        foreach($arr as $v){
            if($v['sys_name']==$sys_name){
                return $v['id'];
            }
        }
        return false;
    }
     
    
	
}
