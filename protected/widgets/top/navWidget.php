<?php
namespace top;
use Ivy\core\Widget;
use Ivy\core\CException;
class NavWidget extends Widget
{
    
	public function run($active=null) {
		$nav = \admin\NavModel::model()->where('type = 1')->order(array('ord'=>'desc'))->findAll();;
        $way = false;
        $navTree = $this->treeNavData($nav);
        if($active){
            $way = $this->getTreeWay($navTree,$active);
        }

        $this->data=array('side_bar_str'=>$this->getSideBarStr($navTree,$way));
        $res = $this->render();
        return $res;
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
     * sidebar 字符串构造
     * $arr 节点树 
     * $way 需要展开的路径节点 
     */
     public function getSideBarStr($arr,$way=false){
        $str = '';
        foreach($arr as $nav){
            $str .= '<li';
            $li_class=array();
            $active=($way&&in_array($nav['id'],$way));
            if($active){
                $li_class[]='active';
            }
            if($nav['fid']==0&&$nav==$arr[0]){
                $li_class[]='start';
            }
            if($nav['fid']==0&&$nav==$arr[count($arr)-1]){
                $li_class[]='last';
            }
            $str.=' class="'.implode(' ', $li_class).'" >'; //li标签结束

            if($nav['uri']){
                $url = $this->url($nav['uri']);
            } else{
                $url = 'javascript:;';
            }
            $str.='<a href="'. $url.'">';
            if(!empty($nav['icon'])){
                $str.='<i class="icon-'.$nav['icon'].'"></i>';
            }
            if($nav['fid']==0){
                $str.='<span class="title">'.$nav['show_name'].'</span>';
                if($active) $str.='<span class="selected"></span>';
            }else{
                $str.=$nav['show_name'];
            }
            if(!empty($nav['children'])){
                if($active){
                    $str.='<span class="arrow open"></span>';
                }else{
                    $str.='<span class="arrow"></span>';
                }
            }
            $str.='</a>';
            if(!empty($nav['children'])){
                if($active){
                    $str.='<ul class="sub-menu">';
                }else{
                    $str.='<ul class="sub-menu" style="display:none;">';
                }
                $str.=$this->getSideBarStr($nav['children'],$way);
                $str.='</ul>';
            }
            $str.='</li>';
        }
        return $str;  
    }

   


}