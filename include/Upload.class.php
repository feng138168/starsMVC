<?php
/**
 * Author: stars
 * Date: 2017/3/22 0022
 */
!defined('ENTER_KEY') ? exit('invalid enter key') : '';

/**
 * 操作文件类
 * Class File
 */
class Upload {
    protected static $ins = null;

    protected $max_upload_filesize  = "";
    protected $error                = '';
    protected $tmp_name             = '';                       //临时文件
    protected $real_name            = '';                       //原始文件名称
    protected $file_name            = '';	                    //生成的文件完整路径
    protected $mime_type            = '';                       //MIME类型
    protected $file_size            = '';                       //文件大小
    protected $file_ext             = '';						//文件扩展名
    protected $allow_exts           = 'gif,png,jpg,jpeg';		//允许上传的类型
        protected $is_image         = false;                    //是否是图片类型文件
    protected $image_path           = '';                       //文件上传路径
    protected $rand_name            = true;                     //是否使用随机文件名
    protected $fileposition;                                    //文件的绝对目录
    protected $file_path            = '';                       //文件上传目录

    public function __construct(){
        $config = Config::getIns();
        $this->max_upload_filesize = $config->upload_max_size*1024*1024;
        $this->fileposition = __ROOT__;
    }

    public static function getIns(){
        if(self::$ins instanceof self){
            return self::$ins;
        }else{
            return new self();
        }
    }


    /**
     * 文件上传
     * @param string $file
     * @param bool $thumb
     * @param bool $watermark
     */
    public function upload($file = '' ,$file_upload_savepath = false, $thumb = false , $watermark = false){
        set_time_limit(0);
        if($this->checkUpload($file)){
            $real_thumb = false;
            $real_watermark = false;
            if($this->is_image){
                $savepath = $this->image_path ? $this->image_path :"uploads/";
                $real_thumb = $thumb;
                $real_watermark = $watermark;
            }else{
                $savepath = $this->file_path?$this->file_path:'uploads';
            }
            if($file_upload_savepath){
                $savepath .= $file_upload_savepath."/";
            }
            $savepath .=date('Ymd',time()).'/';
            $file_name = $this->rand_name ? uniqid().".".$this->file_ext : $this->real_name;
            if(!@is_dir($this->file_position.$savepath)){
                @mkdir($this->file_position.$savepath, 0777, true);
            }
            $this->file_name = $savepath .=$file_name;
            if($real_thumb){
                //生成缩略图
            }elseif($real_watermark){
                //生成水印图
            }else{
                if(self::getOS() == 'Linux'){
                    $mv = move_uploaded_file($this->tmp_name,$this->fileposition.$savepath);
                }else{
                    $mv = move_uploaded_file($this->tmp_name,$this->fileposition.$savepath);
                    //应该处理windo中文乱码问题
                }
                if(!$mv){
                    $this->error = "upload failed ";
                    return false;
                }
            }
            return $this->file_name;
        }
        return $this->error;
    }

    /**
     * 检查文件是否合格上传
     * @param string $file
     * @return bool
     */
    private function checkUpload($file=''){
        if($file && $file['error'] == UPLOAD_ERR_OK){
            $this->tmp_name     = $file['tmp_name'];
            $this->real_name    = $file['name'];
            $this->file_ext     = $this->getExt($file['name']);
            $this->mime_type    = $file['type'];
            $this->file_size    = $file['size'];
            if(in_array($this->file_ext,['bmp','png','jpg','jpeg','gif'])){
                $this->is_image = true;
            }
            if(!in_array($this->file_ext,explode(',',$this->allow_exts))){
                //错误文件类型
                $this->error = "file type is illegal";
                return false;
            }elseif($file['size'] > $this->max_upload_filesize){
                $this->error = "file size is too large";
                return false;
            }else{
                if(!is_uploaded_file($this->tmp_name)){
                    //非法上传
                    $this->error = "file source is Invalid";
                }
            }
        }else{
            $this->error = "file not select";
            return false;
        }
        return true;
    }

    /**
     * 获取文件后缀名
     * @param $fileRealName
     * @return string
     */
    private function getExt($fileRealName){
        $pathinfo = pathinfo($fileRealName);
        return strtolower($pathinfo['extension']);
    }

    /**
     * 获取电脑系统
     * @return string
     */
    private static function getOS(){
        if(PATH_SEPARATOR == ':'){
            return 'Linux';
        }else{
            return 'Windows';
        }
    }

    /**
     * 允许上传文件的类型
     * @param $ext
     */
    protected function setFileExt($ext){
        $this->file_ext = $ext;
    }
}