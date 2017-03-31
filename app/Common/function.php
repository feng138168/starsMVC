<?php
/**
 * Author: stars
 * Date: 2017/3/22 0022
 */
function make_error_json($errno,$errmsg){
    echo json_encode(["errno"=>$errno,"errmsg"=>$errmsg]);
    exit;
}