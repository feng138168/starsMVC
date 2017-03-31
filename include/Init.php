<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */

!defined('ENTER_KEY') ? exit('invalid enter key') : '';

//定义项目初始化目录
define("__ROOT__",str_replace("\\","/",dirname(dirname(__FILE__)))."/");
header("Content-type:text/html;charset=utf-8");
include __ROOT__."core/function.php";
if(file_exists(__ROOT__."app/Common/function.php")){
    include_once __ROOT__."app/Common/function.php";
}
/**
 * 自动加载控制器和模型类
 */
function __autoload($class){
    $core = __ROOT__."include/".$class.".class.php";
    //引入公共方法
    if(file_exists($core) ){
        require $core;
    }else{
        $module = Route::getIns()->getUrl() ? Route::getIns()->getUrl()[0] : "Home";
        /*$controller = __ROOT__ . "app/".$module."/Controller/". $class .'.class.php';
        $model      = __ROOT__ . "app/".$module."/Model/". $class .'.class.php';
        if (file_exists($controller) && strpos($class,"Controller")) {
            include($controller);
        } elseif (file_exists($model) && strpos($class,"Model")) {
            include $model;
        }else{
            exit("<h1>403 Forbiden</h1> ");
        }*/
        $moduleDir =  __ROOT__ . "app/".$module;
        if(is_dir($moduleDir)){
            if(file_exists($moduleDir."/Common/function.php")){
                include_once $moduleDir."/Common/function.php";
            }
            if(strpos($class,"Controller")){
                $controller = __ROOT__ . "app/".$module."/Controller/". $class .'.class.php';
                if(file_exists($controller)) include($controller);
                else exit("<h1>controller not find</h1>");
            }elseif(strpos($class,"Model")){
                $model      = __ROOT__ . "app/".$module."/Model/". $class .'.class.php';
                if(file_exists($model)) include($model);
                else exit("<h1>model not find</h1>");
            }else{
                exit("<h1>403 Forbiden</h1>");
            }
        }else{
            exit("<h1>module not find</h1>");
        }
    }
}
if(debug === true){
    error_reporting(E_ALL);
}else{
    error_reporting(0);
    ini_set("log_errors",'on');
}

if (get_magic_quotes_gpc()) {
    $_POST      =   isset($_POST)     ? $this->stripSlashesDeep($_POST )      : '';
    $_COOKIE    =   isset($_COOKIE)   ? $this->stripSlashesDeep($_COOKIE)     : '';
    $_SESSION   =   isset($_SESSION)  ? $this->stripSlashesDeep($_SESSION)    : '';
}
Route::getIns()->run();   //开启路由


