<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
/**
 * 日志类
 * Class Log
 */
class Log{
    const LOGFILE = 'curr.log';

    /**
     * 写日志
     * @param $info 日志内容
     */
    public static function write($info){
        $info   = $info ."\t".date("Y-m-d H:i:s",time()). "\r\n";
        $log    = self::isBackup();    //计算日志文件的地址
        $fh     = fopen($log,'ab');
        fwrite($fh,$info);
        fclose($fh);
    }

    /**
     * 备份日志
     * @return bool
     */
    public static function backup(){
        $module = Route::getIns()->url()['module'];
        $log = __ROOT__.'app/Runtime/'.$module.'/log/'.self::LOGFILE;
        $backupNmae = 'app/Runtime/'.$module.'/log/'.date("ymd").mt_rand(100000,999999);
        return rename($log,$backupNmae);
    }

    /**
     * 读取日志大小，是否应该备份
     * @return string
     */
    public static function isBackup(){
        $log = __ROOT__.'app/Runtime/'.Route::getIns()->url()['module'].'/log';
        if(!is_dir($log)){
            echo 1;
            mkdir($log,0777,true);
        }
        $log = $log.'/'.self::LOGFILE;
        if( ! file_exists($log) ){
            touch($log);    //快速建立文件
            return $log;
        }

        $size = filesize($log);
        if($size <= 1024*1024){
            return $log;
        }

        //大于1M,备份起来
        if(self::backup()){
            return $log;
        }else{
            touch($log);
            return $log;
        }
    }
}