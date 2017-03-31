<?php
/**
 * Author: stars
 * Date: 2017/3/21 0021
 */
class QrcodeController extends Controller{


    /**
     * 生成名片
     */
    public function index()
    {
        header("Access-Control-Allow-Origin:*");
        include __ROOT__ . "unit/phpqrcode/phpqrcode.php";
        include __ROOT__ . "unit/wechatAPI.php";
        $date = date("Y-m-d", time());
        $time = date("H:i:s", time());
        $data['member_name'] = $_POST['member_name'];
        $data['member_phone'] = $_POST['member_phone'];
        $data['member_email'] = $_POST['member_email'];
        $data['member_tel'] = $_POST['member_tel'];
        $data['member_job'] = $_POST['member_job'];
        //$data['member_position']= $_POST['member_position'];
        $data['member_company'] = $_POST['member_company'];
        $data['member_address'] = $_POST['member_address'];
        $data['member_note'] = $_POST['member_note'];
        $data['c_time'] = time();
        $memberObj = new MemberModel();
        try{
            if ($lastInsId = $memberObj->addVcard($data)) {
                $value = <<<BOF
BEGIN:VCARD
VERSION:3.0
FN:{$data['member_name']}
TEL:{$data['member_phone']}
TEL;CELL;VOICE:{$data['member_tel']}
EMAIL;PREF;INTERNET:{$data['member_email']}
ORG;CHARSET=utf8:{$data['member_company']}
TITLE:{$data['member_job']}
ADR;WORK:{$data['member_address']}
REV:{$date}T{$time}Z
NOTE:{$data['member_note']}
PHOTO;VALUE=uri:http://192.168.1.201/stars/test.png
END:VCARD
BOF;
                    $errorCorrectionLevel = "L";
                    $matrixPointSize = "4";
                    $QRcode = new QRcode();
                    $rootpath = "./uploads/";
                    $savepath = "qrcode/";
                    $savename = uniqid() . ".png";
                    $QRcode->png($value, $rootpath . $savepath . $savename, $errorCorrectionLevel, $matrixPointSize);// 生成本地图片
                    unset($data);
                    $param['data'] = ['member_qrcode' => $savepath . $savename];
                    $param['where'] = "member_id = " . $lastInsId;
                    if ($memberObj->updateVcard($param)) {
                        make_error_json(0, ['member_id' => $lastInsId, 'member_qrcode' => $param['data']['member_qrcode']]);
                    }
                }
        }catch (Exception $e){
            log::write($e->getMessage());
        }
            make_error_json(2001, "insert error");
        }


}