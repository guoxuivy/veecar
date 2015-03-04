<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
namespace admin;
use Ivy\core\lib\Image;
class MainController extends \CController {
    /**
	 * head载入
	 */
	public function headAction(){
        $this->view->assign('page_title','V-CAR 微改装 车生活')->display();
	}
    
    /**
	 * end body载入
	 */
	public function footAction(){
        $this->view->assign()->display();
	}
    
    
    /**
	 * 内部 head载入
	 */
	public function inheadAction(){
        $this->view->assign()->display();
	}
    /**
     * 验证码输出
     */
    public function verifyAction(){
        $w = isset($_GET['w']) ? (int) $_GET['w'] : 50;
        $h = isset($_GET['h']) ? (int) $_GET['h'] : 24;
        Image::buildImageVerify(4, 1, 'png', $w, $h);
    }
    
    /**
	 * SIDEBAR MENU载入
	 */
	public function menuAction($active=null){
	   
        $nav = NavModel::model()->where('type = 1')->order(array('ord'=>'desc'))->findAll();;
        $way = false;
        $navTree = $this->treeNavData($nav);
        if($active){
            $way = $this->getTreeWay($navTree,$active);
        }
        $this->view->assign(array(
            'side_bar_str'=>$this->getSideBarStr($navTree,$way)
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


    
    /**
	 * 登录
	 */
	public function loginAction(){
        if($this->isPost){
            if (isset($_POST['verify_code']) && \Ivy::app()->user->getState('verify') != md5($_POST['verify_code'])) {
                $this->view->assign('error',"验证码错误啦，再输入吧")->display();die;
            }
            $user = UserModel::model()->find("account = '{$_POST['username']}'");
            if($user&&$user->password===md5($_POST['password'])){
                \Ivy::app()->user->login($user);
                if(isset($_POST['remember'])&&$_POST['remember']=='1'){
                    $this->rememberLogin($user);   
                }
                $user->login_time=time();
                $user->save();
                if(\Ivy::app()->user->getReturnUrl()){
                    $this->redirect(\Ivy::app()->user->getReturnUrl());
                }else{
                    $this->redirect('admin');
                }
            }else{
               $this->view->assign('error',"用户名或密码错误！")->display();die;
            }
        }else{
            //自动登录检测
            if($this->autoLogin()){
                if(\Ivy::app()->user->getReturnUrl()){
                    $this->redirect(\Ivy::app()->user->getReturnUrl());
                }else{
                    $this->redirect('admin');
                }
            }else{
                $this->view->assign()->display();die;
            }
            
        }
	}
    /**
	 * 登出
	 */
	public function logoutAction(){
        \Ivy::app()->user->logout();
        setcookie('auth', 'DELETED!', time());
        $this->redirect('admin/main/login');
	}
    /**
	 * 记住登录
	 */
	private function rememberLogin($user){
        $salt = 'AUTOLOGIN';
        $identifier = md5($salt . md5($user->account . $salt));
        $token = md5(uniqid(rand(), TRUE));
        $timeout = time() + 60 * 60 * 24 * 7;//7天有效
        setcookie('auth', "$identifier:$token", $timeout);
        $user->identifier=$identifier;
        $user->token=$token;
        $user->timeout=$timeout;
        $user->save();
	}
    /**
	 * 自动登录 判断
	 */
	private function autoLogin(){
        $salt = 'AUTOLOGIN';
        $clean = array();
        $now = time();
        if(!isset($_COOKIE['auth'])) return false;
        list($identifier, $token) = explode(':', $_COOKIE['auth']);
        
        if (ctype_alnum($identifier) && ctype_alnum($token))
        {
            $clean['identifier'] = $identifier;
            $clean['token'] = $token;
        }else{
            return false;
        }

        $user = UserModel::model()->where("identifier = '{$clean['identifier']}'")->find();
        if($user&&$user->token==$clean['token']&&$now < $user->timeout){
            \Ivy::app()->user->login($user);
        }else{
            return false;
        }
        $user->login_time=time();
        $user->save();
        return true;
	}
    
    
	
}
