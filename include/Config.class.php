<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
/**
 * 配置文件类
 * Class Config
 */
class Config{
    protected  static $ins = null;

    protected $data = array();

    final protected function __construct(){
        include "./conf/config.php";
        $this->data = $_config;
    }

    final protected function __clone(){}

    public static function getIns(){
        if(self::$ins instanceof self){
            return self::$ins;
        }else{
            return new self();
        }
    }

    /**
     * 获取配置文件
     * @param $key
     * @return null/value
     */
    public function __get($key){
        if(array_key_exists($key,$this->data)){
            return $this->data[$key];
        }else{
            return null;
        }
    }

    /**
     * 动态添加key
     * @param $key
     * @param $value
     */
    public function __set($key,$value){
        $this->data[$key] = $value;
    }
}