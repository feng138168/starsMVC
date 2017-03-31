<?php
/**
 * Author: stars
 * Date: 2017/3/17
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
/**
 * 模型类
 * Class Model
 */
class Model {
    protected $db;
    protected $tableName;

    public function __construct(){
        $this->db           = Mysql::getIns();
        $this->tableName    = Config::getIns()->db_prefix.strtolower(substr(get_class($this),0,-5));
    }

}