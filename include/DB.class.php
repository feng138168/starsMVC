<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */

/**
 * DB类的抽象类，为mysql，mysqli和pdo做一个规范
 * Class DB
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
abstract class DB{
    /**
     * 链接服务器
     * @param $host
     * @param $user
     * @param $pwd
     * @return bool
     */
    public abstract function connect($host,$user,$pwd);

    /**
     * 发送查询
     * @param $sql  查询语句
     * @return mixed    bool或者返回资源
     */
    public abstract function query($sql);

    /**
     * 查询多行
     * @param  $sql sql语句
     * @return mixed    array/bool
     */
    public abstract function getAll($sql);

    /**
     * 查询单行
     * @return mixed array/bool
     */
    public abstract function getRow();

    /**
     * 查询单个数据
     * @return mixed array/bool
     */
    public abstract function getOne();

    /**
     * 自动拼接sql
     * @param $table
     * @param $data
     * @param string $act
     * @param string $where
     * @return mixed  array/bool
     */
    public abstract function autoExecute($table,$data,$act='insert',$where='');
}