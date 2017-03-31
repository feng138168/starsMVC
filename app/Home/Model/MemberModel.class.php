<?php
/**
 * Author: stars
 * Date: 2017/3/22 0022
 */
class MemberModel extends Model{
    /**
     * @param $param 整理的数据串
     * @return bool|mysqli_result
     */
    public function addVcard($param){
        if($this->db->autoExecute($this->tableName,$param)){
            return $this->db->getLastInsId();
        }
        return false;
    }

    /**
     * 更新我的名片表
     * @param $param
     * @return bool
     */
    public function updateVcard($param){
        if($this->db->autoExecute($this->tableName,$param['data'],'update',$param['where'])) return true;
        return false;
    }
}