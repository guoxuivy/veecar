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
        //$result = \Ivy::app()->user->checkAccess($this->route);
        $result = true;

        if($result==false){
            $route = implode('->', $this->route->getRouter());
            throw new CException('没有授权该操作！'.$route);
        }
    }

    /**
     * 分配授权项
     * @return [type] [description]
     */
    public function assignAction(){
    	$user_class = $this->_rbac_confit["userclass"];
    	$id_str = $this->_rbac_confit["userid"];
    	$user_name = $this->_rbac_confit["username"];
    	$user_model = $user_class::model();

    	$u_list = $user_model->field(array($id_str,$user_name))->findAll();
    	$role_list = AuthItem::model()->field('name')->where('type=2')->findAll();
    	$task_list = AuthItem::model()->field('name')->where('type=1')->findAll();

    	$have_role_list=Assignment::model()->where("userid=".$userId)->findAll();
    	

    	$this->view->assign(array(
    		'user_name_col'=>$user_name,
    		'user_id_col'=>$id_str,
    		'u_list'=>$u_list,
    		'role_list'=>$role_list,
    		'task_list'=>$task_list,
    	))->display();
    }

    /**
	 * 用户已绑定角色显示json
	 */
	public function jsonRoleAction() {
		$userId=$_REQUEST['user_id'];
		$have_role_list=Assignment::model()->field('itemname')->where("userid=".$userId)->findAll();
    	$role_list = AuthItem::model()->field('name')->findAll('type=2');
    	$all=self::i_array_column($role_list,'name');
    	$have=self::i_array_column($have_role_list,'itemname');
    	$no=array();
    	foreach ($all as $value) {
    		if(!in_array($value, $have))
    			$no[]=$value;
    	}
		$this->ajaxReturn('200','ok',array('have'=>$have,'no'=>$no));
	}

	/**
	 * 用户角色变更json
	 */
	public function changeRoleAction() {
		$userId=$_REQUEST['user_id'];
		$roles=$_REQUEST['roles'];
		$act=$_REQUEST['act'];//add 添加 remove 移除

		if($act=='add'){
			Assignment::model()->addRoles($userId,$roles);
		}elseif($act=='remove'){
			Assignment::model()->removeRoles($userId,$roles);
		}else{
			throw new CException('无效操作！');
		}
		$this->ajaxReturn('200','ok');
	}


	/**
	 * 角色已绑定任务显示json
	 */
	public function jsonTaskAction() {
		$role=$_REQUEST['role'];
		$have_task_list=ItemChild::model()->field('child')->findAll("parent='{$role}'");
    	$task_list = AuthItem::model()->field('name')->findAll('type=1');
    	$all=self::i_array_column($task_list,'name');
    	$have=self::i_array_column($have_task_list,'child');
    	$no=array();
    	foreach ($all as $value) {
    		if(!in_array($value, $have))
    			$no[]=$value;
    	}
		$this->ajaxReturn('200','ok',array('have'=>$have,'no'=>$no));
	}
	/**
	 * 子集变更json
	 */
	public function changeChildAction() {
		$parent=$_REQUEST['parent'];
		$childs=$_REQUEST['childs'];
		$act=$_REQUEST['act'];//add 添加 remove 移除

		if($act=='add'){
			ItemChild::model()->addChilds($parent,$childs);
		}elseif($act=='remove'){
			ItemChild::model()->removeChilds($parent,$childs);
		}else{
			throw new CException('无效操作！');
		}
		$this->ajaxReturn('200','ok');
	}


	/**
	 * 角色已绑定任务显示json
	 */
	public function jsonMethodAction() {
		$task=$_REQUEST['task'];
		$have_method_list=ItemChild::model()->field('child')->findAll("parent='{$task}'");
    	$method_list = AuthItem::model()->field('name')->findAll('type=0');
    	$all=self::i_array_column($method_list,'name');
    	$have=self::i_array_column($have_method_list,'child');
    	$no=array();
    	foreach ($all as $value) {
    		if(!in_array($value, $have))
    			$no[]=$value;
    	}
		$this->ajaxReturn('200','ok',array('have'=>$have,'no'=>$no));
	}


	public function navAction() {
        $this->view->assign()->display();
	}

	/**
	 * item列表
	 */
	public function indexAction() {
		$page=$_REQUEST['page']?$_REQUEST['page']:1;
		$itemlist = AuthItem::model()->page($page,30)->getPagener();
		//$result= $this->getAllControllers();
        $this->view->assign('itemlist',$itemlist)->display();
	}

	/**
	 * 权限查看
	 */
	public function showAction() {
		if($this->isAjax){
			$userId=$_REQUEST['userid'];
			$role_list=Assignment::model()->field('itemname')->findAll("userid=".$userId);
			foreach ($role_list as &$value) {
				$task_list=$this->getChild($value['itemname']);
				foreach ($task_list as &$task) {
					$method_list=$this->getChild($task);
					$task=array('name'=>$task,'method_list'=>$method_list);
					unset($task);
				}
				$value['task_list']=$task_list;
				unset($value);
			}
			$this->ajaxReturn('200','ok',$role_list);

		}else{
			$user_class = $this->_rbac_confit["userclass"];
	    	$id_str = $this->_rbac_confit["userid"];
	    	$user_name = $this->_rbac_confit["username"];
	    	$user_model = $user_class::model();
	    	$u_list = $user_model->field(array($id_str,$user_name))->findAll();
			$this->view->assign(array(
	    		'user_name_col'=>$user_name,
	    		'user_id_col'=>$id_str,
	    		'u_list'=>$u_list,
    		))->display();
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
	}

	/**
	 * 编辑一条记录
	 */
	public function editItemAction() {
		AuthItem::model()->edit($_POST['oname'],$_POST['name']);
		if($this->isAjax){
			die('ok');
		}else{
			$this->redirect('index');
		}
	}
	/**
	 * 删除一条记录
	 */
	public function delItemAction() {
		AuthItem::model()->deleteByPk($_POST['oname']);
		if($this->isAjax){
			die('ok');
		}else{
			$this->redirect('index');
		}
	}

	/**
	 * 自动建立权限点
	 */
	public function autoAction() {
		$result= $this->getAllControllers();
        $this->view->assign($result)->display();
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

			if($module_name=='rbac') continue;

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



	static public function i_array_column($input, $columnKey, $indexKey=null){
        if(!function_exists('array_column')){ 
            $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
            $indexKeyIsNull            = (is_null($indexKey))?true :false; 
            $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
            $result                         = array(); 
            foreach((array)$input as $key=>$row){ 
                if($columnKeyIsNumber){ 
                    $tmp= array_slice($row, $columnKey, 1); 
                    $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
                }else{ 
                    $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
                } 
                if(!$indexKeyIsNull){ 
                    if($indexKeyIsNumber){ 
                      $key = array_slice($row, $indexKey, 1); 
                      $key = (is_array($key) && !empty($key))?current($key):null; 
                      $key = is_null($key)?0:$key; 
                    }else{ 
                      $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                    } 
                } 
                $result[$key] = $tmp; 
            } 
            return $result; 
        }else{
            return array_column($input, $columnKey, $indexKey);
        }
    }

}
