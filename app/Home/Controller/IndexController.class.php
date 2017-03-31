<?php
/**
 * Author: stars
 * Date: 2017/3/16
 */
class IndexController extends Controller {

    public function index(){
        echo "1";
    }

    public function test1(){
        if($_POST){
            var_dump($_FILES);
            var_dump(Upload::getIns()->upload($_FILES['file'],"Home/memberImg"));
        }else{
            $this->fetch("public/test");
        }
    }

}