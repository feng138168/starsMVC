<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
/**
 * 路由转发类
 * Class route
 */
class Route{

    /**
     * 路由类
     * @var
     */
    protected static $ins;

    /**
     * 实例化路由类
     * @return Route
     */
    public static function getIns(){
        if(self::$ins instanceof self ){
            return self::$ins;
        }else{
            self::$ins = new self();
            return self::$ins;
        }
    }

    /**
     * 获取路由中的信息
     * @return mixed
     */
    public function url(){
        $result['module']     =   "Home";
        $result['controller'] =   "Index";
        $result['action']     =   "index";
        $result['param']      = array();

        $url = $this->getUrl() ? $this->getUrl() : false;
        if($url){
            //模块
            $result['module'] = ucfirst($url[0]);
            array_shift($url);
            //控制器
            if($url){
                $result['controller'] = ucfirst($url[0]);
                array_shift($url);
            }
            //方法
            if($url){
                $result['action'] = ucfirst($url[0]);
                array_shift($url);
            }
            //参数
            $result['param'] = $url ? $url : array();
        }
        return $result;
    }


    /**
     * 获取url并整理成module/controller/action
     * @return array
     */
    public function getUrl(){
        $urlArr = explode("/",$_SERVER['PHP_SELF']);
        $urlArr = array_filter($urlArr);

        if(!in_array("index.php",$urlArr)) return $urlArr;
        foreach($urlArr as $k=>$v){
            if($v != 'index.php'){
                unset($urlArr[$k]);
            }else{
                unset($urlArr[$k]);
                break;
            }
        }
        return array_values($urlArr);
    }

    /**
     * 执行路由跳转
     */
    public function run(){
        $result = $this->getIns()->url();
        $controller = $result['controller']."Controller";
        $dispatch = new $controller($result['controller'],$result['action']);
        // 如果控制器存和动作存在，这调用并传入URL参数
        if ((int)method_exists($controller, $result['action'])) {
            call_user_func_array(array($dispatch, $result['action']), $result['param']);
        } else {
            exit("controller or action error ");
        }
    }
}