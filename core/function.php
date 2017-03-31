<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */

/**
 * 获取请求参数
 * @param $key 参数的键
 * @param $method 请求方法
 * @return string
 */
function get_param($key,$method){
    if(strtolower($method) === 'get'){
        $urlArr = explode("/",$_SERVER['PHP_SELF']);
        if($urlArr == "/index.php"){
            $urlArr = "Home/Index/index";
        }
        $urlArr = array_filter($urlArr);
        foreach($urlArr as $k=>$v){
            if($v != 'index.php'){
                unset($urlArr[$k]);
            }else{
                unset($urlArr[$k]);
                break;
            }
        }
        if(count($urlArr)>3){
            $i = 0;
            foreach($urlArr as $k=>$v){
                if($i<3){
                    $i++;
                    unset($urlArr[$k]);
                }
                else{ break;}
            }
        }
        return Filter::stripSlashesDeep(@$urlArr[array_search($key,$urlArr)+1]);
    }elseif(strtolower($method) === 'post'){
        return Filter::stripSlashesDeep($_POST[$key]);
    }
}

/**
 * url方法跳转
 * @param $url
 * @param null $param
 */
function url($url,$param = null){
    $url ? $url : exit("invalid param");
    $serverRoot = explode("index.php",$_SERVER['PHP_SELF']);
    if(Config::getIns()->pathinfo){
        $baseUrl = "http://".$_SERVER['SERVER_NAME'].$serverRoot[0];
    }else{
        $baseUrl = "http://".$_SERVER['SERVER_NAME'].$serverRoot[0]."index.php/";
    }
    $routeObj = Route::getIns();
    if(strpos($url,"/")){
        $url = explode("/",$url);
        $len = count($url);
        if($len == 2){
            //加上默认的Home模块
            @$routeUrl = $routeObj->url()['module']."/".$url[0]."/".$url[1];
        }elseif($len == 3){
            @$routeUrl =$url[0]."/".$url[1]."/".$url[2];
        }
    }else{
        $routeUrl = $routeObj->url()['module']."/".$routeObj->url()['controller']."/".$url;
    }
    //拼接参数
    $jumpUrl = $baseUrl.$routeUrl;
    //return $jumpUrl;
    unset($baseUrl);
    unset($routeUrl);
    if($param == null){
        header("Location:".$jumpUrl);
        exit;
    }elseif(is_array($param)){
        foreach($param as $k=>$v){
            $jumpUrl .="/".$k."/".$v;
        }
        header("Location:".$jumpUrl);
        exit;
    }


    /**
     * 获取配置文件参数
     * @param $key
     * @return null
     */
    function config($key){
        return Config::getIns()->$key;
    }
}