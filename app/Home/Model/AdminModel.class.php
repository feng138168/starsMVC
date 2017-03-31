<?php
/**
 * Author: stars
 * Date: 2017/3/17
 */
class AdminModel extends Model{
    public function test(){
        $sql = "select * from stars_admin1";
        return mysqli_fetch_assoc($this->db->query($sql));
    }
}