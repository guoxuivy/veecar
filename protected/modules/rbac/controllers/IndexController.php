<?php
/**
 * 权限管理 扩展 前端逻辑控制器
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 * @see 
 * 
 */
namespace rbac;
use Ivy\core\CException;
class IndexController extends AuthController {
	/**
     * 本控制器的权限认证 重写该方法
     * @return [type] [description]
     */
    public function actionBefore(){
        $result = \Ivy::app()->user->checkAccess($this->route);
        $result = true;

        if($result==false){
            $route = implode('->', $this->route->getRouter());
            throw new CException('没有授权该操作！'.$route);
        }
    }

	/**
	 * 自动权限管理检测入口
	 */
	public function indexAction() {
		$itemlist = AuthItem::model()->getPagener();
		$result= $this->getAllControllers();
        $this->view->assign('itemlist',$itemlist)->display();
	}

	/**
	 * 批量添加操作
	 */
	public function addMethodsAction() {
		$res = AuthItem::model()->addMethods($_POST['list']);
		if($res){
			die('ok');
		}else{
			die('error');
		}
	}
	
	/**
	 * 添加一条记录
	 */
	public function addItemAction() {
		$model = new AuthItem;
		$model->attributes=$_POST;
		$model->save();
		$this->redirect('index');
		//$this->view->assign()->display('index');
	}

	/**
	 * 自动建立权限点
	 */
	public function autoAction() {
		$result= $this->getAllControllers();
        $this->view->assign($result)->display();
	}
	

	/**
	 * 获取控制器下权限点列表
	 * @return json 
	 */
	public function autoCreateItemsAction() {
		$group=$_REQUEST['group'];
		$class=$_REQUEST['class'];
		$class_name = '\\'.$group.'\\'.$class;

		if(empty($group)){
			$class_name = '\\'.$class;
		}else{
			$class_name = '\\'.$group.'\\'.$class;
		}

		$ReflectedClass = new \ReflectionClass($class_name); // 2级控制器检测 非分组模式
		$ReflectedClassName=$ReflectedClass->getName();
		$controller= substr($class,0,-10);
		$action_list=array();
		foreach ($ReflectedClass->getMethods() as $method) {
			
			if($ReflectedClassName==$method->class && "Action"===substr($method->name,-6)){
				$action=substr($method->name,0,-6);
				if(empty($group)){
					$item = $controller.'#'.$action;
				}else{
					$item = $group.'@'.$controller.'#'.$action;
				}
				$action_list[]=strtolower($item);
			}
		}
		//数据库已经存在的
		$in_list = $this->getHaveItem($controller,$group);

		//获取数据库不存在的操作列表
		$out_list = array();
		foreach ($action_list as $value) {
			if(!in_array($value, $in_list)){
				$out_list[]=$value;
			}
		}
		$this->ajaxReturn(200,'ok',array('in_list'=>$in_list,'out_list'=>$out_list));
	}

	/**
	 * 获取数据库已经收入的操作列表
	 * @param  [type] $controller [description]
	 * @param  [type] $group      [description]
	 * @return [type]             [description]
	 */
	protected function getHaveItem($controller,$group=null){
		if(empty($group)){
			$str = $controller.'#%';
		}else{
			$str = $group.'@'.$controller.'#%';
		}
		$str = strtolower($str);
		$items = AuthItem::model()->findAll(" `name` LIKE '{$str}' ");
		$list=array();
		foreach ($items as $item) {
			$list[]=$item['name'];
		}
		return $list;
	}

	
	/**
	 * 获取所有控制器名称
	 * @return array global_controllers 全局控制器 
	 *               group_controllers 分组控制器
	 */
	protected function getAllControllers(){
		$r_c_list=$m_c_list=array();
		$root_c_list=$module_c_list=array();
		$root_dir = __PROTECTED__.DIRECTORY_SEPARATOR."controllers";
		$module_dir = __PROTECTED__.DIRECTORY_SEPARATOR."modules";
		self::allScandir($root_dir,$root_c_list);
		self::allScandir($module_dir,$module_c_list);
		foreach ($root_c_list as $value) {
			$tmp = explode('\\',$value);
			$c_file=array_pop($tmp);
			$tmp_class= basename($c_file,'.php');
			if(!is_subclass_of('\\'.$tmp_class, '\\rbac\\AuthController')){
				continue;
			}
			$r_c_list[]= basename($value,'.php');
		}

		foreach ($module_c_list as $value) {
			$tmp = explode('\\',$value);
			$c_file=array_pop($tmp);
			array_pop($tmp);
			$module_name=array_pop($tmp);
			$tmp_class= basename($c_file,'.php');

			if(!is_subclass_of('\\'.$module_name.'\\'.$tmp_class, '\\rbac\\AuthController')){
				continue;
			}
			if(isset($m_c_list[$module_name])){
				$m_c_list[$module_name][]=$tmp_class;
			}else{
				$m_c_list[$module_name]=array($tmp_class);
			}
			
		}
		return array('global_controllers'=>$r_c_list,'group_controllers'=>$m_c_list);
	}


	/**
	 * 文件夹遍历
     * 返回所有控制器文件
	 */
	static public function allScandir($file_path='',&$arr) {
	   if(!empty($file_path)){
	       foreach(scandir($file_path) as $dir){
	           if($dir!="."&&$dir!=".."){
	               $f_name=$file_path.DIRECTORY_SEPARATOR.$dir;
	               if(is_dir($f_name)){
	                   self::allScandir($f_name,$arr);
	               }else{
	               		if("Controller.php"===substr($dir,-14)){
	                       array_push($arr,$f_name);
	                    }
	               }
	           }
	       }
	   }
	}

}
