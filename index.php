<?php
$ivy=dirname(__FILE__).'/framework/Ivy.php';
defined('IVY_DEBUG') or define('IVY_DEBUG',false);
//关闭notice级别错误提示
error_reporting(E_ALL & ~E_NOTICE);
require_once($ivy);
$app = Ivy::createApplication()->run();