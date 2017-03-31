<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';
/**
 * mysql类
 * Class Mysql
 */
class Mysql extends DB{
    protected static $ins;
    protected $c;
    protected $conn;
    protected $result;

    final function __construct(){
        $this->c = Config::getIns();
        $this->conn = $this->connect($this->c->host,$this->c->user,$this->c->pwd);
        if(!$this->conn){
            throw new Exception("connect databases error");
        }
    }

    /**
     * 初始化mysql
     * @return mysql
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
     * 链接数据库
     * @param $host
     * @param $user
     * @param $pwd
     * @return mysqli
     */
    public function connect($host,$user,$pwd){
        return mysqli_connect($host,$user,$pwd);
    }


    /**
     * 设置字符集
     */
    protected function setChar(){
        $sql = "set names ".$this->c->charset;
        mysqli_query($this->conn,$sql);
    }

    /**
     * 选择数据库
     * @return bool
     */
    protected function selectDB(){
        return mysqli_select_db($this->conn,$this->c->database);
    }

    /**
     * 发送查询
     * @param 查询语句 $sql
     * @return bool|mysqli_result
     * @throws Exception
     */
    public function query($sql){
        if(!$this->selectDB()){
            throw new Exception("coule not find database ".$this->c->database);
        }
        $this->setChar();
        $this->result = mysqli_query($this->conn,$sql);

        if(!$this->result){
            throw new Exception("Invalid query: ".$sql);
        }
        return $this->result;
    }

    /**
     * 查询多行数据
     * @param sql语句 $sql
     * @return bool|mysqli_result
     * @throws Exception
     */
    public function getAll($sql){
        return $this->query($sql);
    }

    /**
     * 查询单行数据
     * @return array|null
     */
    public function getRow(){
        if(is_object($this->result)){
            $row = mysqli_fetch_assoc($this->result);
        }else{
            $row = $this->result;
        }
        return $row;
    }


    /**
     * 查询单个数据
     */
    public function getOne(){}

    /**
     * 插入数据
     * @param $table
     * @param $data
     * @param string $act
     * @param null $where
     * @return bool|mysqli_result
     * @throws Exception
     */
    public function autoExecute($table,$data,$act = 'insert',$where = null){
        if($act == 'insert'){
            $sql = "INSERT INTO $table";
            $filed = array();
            $values = array();
            foreach($data as $k=>$v){
                array_push($filed,$k);
                array_push($values,$v);
            }
            $sql .="(".implode(',',$filed).") VALUES (' ".implode("','",$values)."')";
        }else{
            $sql = "UPDATE $table SET ";
            foreach($data as $k=>$v){
                $sql .="$k = '$v',";
            }
            //去掉最后的逗号   ,
            $sql = substr($sql,0,-1)." WHERE ".$where;
        }
        return $this->query($sql);
    }

    /**
     * 取得整个结果集
     * @return array|bool
     */
    public function getResultSet(){
        if($this->result==null){
            return false;
        }
        $this->rowSet = array();
        while($row = mysqli_fetch_assoc($this->result)){
            array_push($this->rowSet,$row);
        }

        return $this->rowSet;
    }

    /**
     * 获取上一次插入id
     * @return int|string
     */
    public function getLastInsId(){
        return mysqli_insert_id($this->conn);
    }
}