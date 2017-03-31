<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */

/**
 * 参数过滤控制器
 * Class filter
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
class Filter{

    /**
     * 参数过滤
     * @param $param
     * @return array|string
     */
    public static function stripSlashesDeep($param){
        if(is_array($param)){
            foreach($param as $k=>$v) {
                if (is_array($v)) {
                    self::stripSlashesDeep($v);
                } else {
                    $param[$k] = stripslashes($v);
                }
            }
        }elseif(is_string($param)){
            $param = stripcslashes($param);
        }

        return $param;
    }
}