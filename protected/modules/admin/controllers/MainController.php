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
	public function loginAction(){
        if($this->isPost){
            $user = UserModel::model()->find("account = '{$_POST['username']}'");
            if($user&&$user->password===md5($_POST['password'])){
                \Ivy::app()->user->login($user);
                if(isset($_POST['remember'])&&$_POST['remember']=='1'){
                    $this->rememberLogin($user);   
                }
                if(\Ivy::app()->user->getReturnUrl()){
                    $this->redirect(\Ivy::app()->user->getReturnUrl());
                }else{
                    $this->redirect('admin');
                }
            }else{
               $this->view->assign('error',"用户名或密码错误！")->display(); 
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
                $this->view->assign()->display();
            }
            
        }
	}
    /**
	 * 登出
	 */
	public function logoutAction(){
        \Ivy::app()->user->logout();
        setcookie('auth', 'DELETED!', time());
        $this->redirect('admin');
	}
    /**
	 * 记住登录
	 */
	private function rememberLogin($user){
        $salt = 'AUTOLOGIN';
        $identifier = md5($salt . md5(\Ivy::app()->user->account . $salt));
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
        $user = UserModel::model()->find("identifier = '{$clean['identifier']}'");
        if($user&&$user->token==$clean['token']&&$now < $user->timeout){
            \Ivy::app()->user->login($user);
        }else{
            return false;
        }
        return true;
	}
    
    
	
}
