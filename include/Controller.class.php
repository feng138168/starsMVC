<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */

/**
 * 控制器类
 * Class Controller
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
class Controller{
    protected $view;

    /**
     * 构造函数，初始化属性
     */
    public function __construct(){
        $this->view = new View();
    }

    /**
     * 模板赋值
     * @param $name
     * @param $value
     */
    public function assign($name, $value)
    {
        $this->view->assign($name, $value);
    }

    /**
     * 渲染模板
     */
    public function fetch($file = null){
        header("Content-type:text/html,charset=utf-8");
        $this->view->fetch($file);
        exit;
    }
}