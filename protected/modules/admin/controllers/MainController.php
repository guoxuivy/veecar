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

    public $layout=null;
  
    /**
     * 验证码输出
     */
    public function verifyAction(){
        $w = isset($_GET['w']) ? (int) $_GET['w'] : 50;
        $h = isset($_GET['h']) ? (int) $_GET['h'] : 24;
        Image::buildImageVerify(4, 1, 'png', $w, $h);
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
