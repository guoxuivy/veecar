<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @since 1.0
 */
class PlugController extends \CController {
	private static  $_PLUGDIR = null;

	 
	public function init() {
		self::$_PLUGDIR = __PROTECTED__.DIRECTORY_SEPARATOR."plugs";
	}
   	/**
	 * 百度编辑器 插件服务端
	 */
	public function ueditorAction() {
		$contro = self::$_PLUGDIR.DIRECTORY_SEPARATOR."ueditor".DIRECTORY_SEPARATOR."controller.php";
		//自动处理上传 且返回结果
		$result = require_once($contro);
		//水印+缩略图
		if(isset($result['state'])&&$result['state']=='SUCCESS'&&in_array($result['type'],array('.png','.jpg','.bmp','.gif'))){
		    $result['thumb'] = $this->imageHandle($result['url'],true);
		}

		/* 输出结果 */
		$result = json_encode($result);
		if (isset($_GET["callback"])) {
		    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
		        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
		    } else {
		        echo json_encode(array(
		            'state'=> 'callback参数不合法'
		        ));
		    }
		} else {
		    echo $result;
		}
	}

	/**
	 * 自动处理图片 水印 + 缩略图
	 * @param $fullName
	 * @return mixed|string
	 */
	protected function imageHandle($fullName,$thumb=false,$water=true){
	    $thumbnail = $fullName;
	    $path = $_SERVER['DOCUMENT_ROOT'];
	    if (substr($fullName, 0, 1) != '/') {
	        $fullName = '/' . $fullName;
	    }
	    $path = $path.$fullName;

	    //图片处理类
	    $tpImage = self::$_PLUGDIR.DIRECTORY_SEPARATOR."ueditor".DIRECTORY_SEPARATOR."tpImage.php";
		require_once($tpImage);

	    $image = new tpImage();
	    $image2 = clone $image;
	    //生成缩略图
	    if($thumb){
	        $image->open($path);
	        $tmp = pathinfo($path);
	        $tmp = $tmp['dirname'].'/'.$tmp['filename'].'_thumb.'.$tmp['extension'];
	        $image->thumb(200,200);
	        $image->save($tmp);
	        $thumbnail = pathinfo($fullName);
	        $thumbnail = $thumbnail['dirname'].'/'.$thumbnail['filename'].'_thumb.'.$thumbnail['extension'];
	    }
	    $watermark=__ROOT__."/public/img/water.png";
	    //生成水印
	    if($water && $watermark && file_exists($watermark)){
	        $image2->open($path);
	        $image2->water($watermark, 9);
	        $image2->save($path);
	    }

	    return $thumbnail;
	}

	
}