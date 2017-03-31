<?php

/**
 * Author: stars
 * Date: 2017/3/17
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
/**
 * 试图类
 * Class View
 */
class View{
    protected $variables = array();
    protected $route;

    public function __construct(){
        $this->route = Route::getIns();
    }

    /**
     * 分配变量
     * @param $name
     * @param $value
     */
    public function assign($name, $value){
        $this->variables[$name] = $value;
    }

    /**
     *  渲染显示
     * @param null $file 文件路径
     */
    public function fetch($file = null){
        extract($this->variables);
        if($file == null){
            $view = __ROOT__ . 'app/'.$this->route->url()['module'].'/View/' . $this->route->url()['controller'] . '/' . $this->route->url()['action'] . '.php';
        }else{
            if(strpos($file,"/")){
                $file = explode("/",$file);
                $view = __ROOT__ . 'app/'.$this->route->url()['module'].'/View/';
                foreach($file as $v){
                    $view .= $v."/";
                }
                $view = substr($view,0,-1).".php";
            }else{
                $view = __ROOT__ . 'app/'.$this->route->url()['module'].'/View/' . $this->route->url()['controller'] . '/' . $file . '.php';
            }
        }
        if (file_exists($view)) {
            include ($view);
        }else{
            echo "template not find";
        }
    }
}