<?php
$ivy=dirname(__FILE__).'/framework/Ivy.php';
defined('IVY_DEBUG') or define('IVY_DEBUG',true);
//屏蔽系统提示于警告
//error_reporting(0);
require_once($ivy);
$app = Ivy::createApplication()->run();
