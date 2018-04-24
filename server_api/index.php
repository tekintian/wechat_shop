<?php
// 应用入口文件
//防止网站被iframe安全设置
#header("X-Frame-Options: ALLOW-FROM SAMEORIGIN");
//设定utf8编码格式
header("Content-Type:text/html; charset=utf-8");
//更新时区
date_default_timezone_set('PRC');
//设置时区
date_default_timezone_set('Asia/Shanghai');
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义应用名字+目录
define('APP_NAME','App');
define('APP_PATH','./App/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';
