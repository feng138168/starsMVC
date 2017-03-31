<?php
/**
 * Author: stars
 * Date: 2016/11/21
 */
class wechatAPI{
    private $appid = '';
    private  $appsectet = '';

    public function __construct($appid='',$appsectet=''){
        $this->appid = $appid;
        $this->appsectet = $appsectet;
        echo $this->appid;
    }
    /**
     * 发送post 或者get请求
     * @param $url  接口的url
     * @param null $data    post请求的数据
     * @return mixed
     */
    public function https_request($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 发送xml数据请求
     * @param $url
     * @param $content
     * @return mixed
     */
    public function sendXML($url,$content){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    /**随机字符串
     * @return string
     */
    public function make_nonceStr(){
        $str = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle($str),0,16);
    }

    /**
     * 获取acces_token
     * @return mixed
     */
    public function get_access_token(){
        if(is_file("./Public/access_token.json")){
            $data = json_decode(file_get_contents('./Public/access_token.json'));
            if($data->expire_time > time() && !empty($data->access_token)){
                return $data->access_token;
            }
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsectet;
        $access_token_json = json_decode(https_request($url));
        $access_token_json->expire_time = time() + 7200;
        $fp = fopen("./Public/access_token.json", "w");
        fwrite($fp, json_encode($access_token_json));
        fclose($fp);
        return $access_token_json->access_token;
    }

    /**
     * 下载二维码图片
     * @param $url  二维码图片的url
     * @return mixed
     */
    public function downImage($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 获取网页授权code
     * @param $redirect_url
     * @param $scope
     * @param string $state
     * @return string
     */
    public function getAuthorization($redirect_url,$scope,$state="STATE"){
        $data = array(
            "appid"=>$this->appid,
            "redirect_uri"=>$redirect_url,
            "response_type"=>"code",
            "scope"=>$scope,
            "state"=>$state
        );
        $queryString = http_build_query($data);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?".$queryString."#wechat_redirect";
        return "<script>window.location.href='{$url}'</script>";;
    }

    /**
     * 拉去网页授权的access_token
     * @param $code
     * @return mixed
     */
    public function getAuthorizationAccessToken($code){
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsectet}&code=".$code."&grant_type=authorization_code";
        $json = $this->https_request($url);
        return $json;

    }
    /**
     * 获取用户信息
     * @param $access_token
     * @param $openId
     * @return mixed
     */
    public function getAuthorizationUserInfo($access_token,$openId){
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openId."&lang=zh_CN";
        $UserInfoJson = $this->https_request($url);
        return $UserInfoJson;
    }

    /**
     * 获取jsapi_ticket
     * @return mixed
     */
    function get_jsapi_ticket(){
        if(is_file(".Public/jsapi_ticket.json")){
            $data = json_decode(file_get_contents("./Public/jsapi_ticket.json"));
            if($data->expire_time > time() && !empty($data->ticket)){
                return $data;
            }
        }
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$this->get_access_token()."&type=jsapi";
        $jsapi_ticket_json = json_decode($this->https_request($url));
        $jsapi_ticket_json->expire_time = time() + 7200;
        $fp = fopen("./Public/jsapi_ticket.json","w");
        fwrite($fp,json_encode($jsapi_ticket_json));
        fclose($fp);
        return $jsapi_ticket_json->ticket;
    }


    /**
     * 生成签名
     * @param $nonceStr
     * @param $jsapi_ticket
     * @param $time
     * @param $url
     * @return string
     */
    public function signature($nonceStr,$jsapi_ticket,$time,$url){
        $tmpArray = array(
            'noncestr'=>$nonceStr,
            'jsapi_ticket'=>$jsapi_ticket,
            'timestamp'=>$time,
            'url'=>$url
        );
        ksort($tmpArray,SORT_STRING);
        $string = http_build_query($tmpArray);
        return sha1(urldecode($string));
    }

    /**
     * 回复客服消息
     * @param $openId
     * @param $content
     * @param $type 文本:"text",图片:"image",语音:"voice",视频:"video",缩略图:"thumb" 音乐:"music" 新闻/图文消息:"news"
     */
    public function customerAPI($openId,$type,$content=array()){
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->get_access_token();
        $data = array(
            'touser' =>$openId,
            'msgtype'=>$type,
            $type=>$content
        );
        $data = json_encode($data);
        return $this->https_request($url,$data);
    }


    /**
     * *****************返回文本消息*********************
     */
    function returnText($FromUserName,$ToUserName,$string){
        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
        $time = time();
        $msgType = "text";
        return printf($textTpl, $FromUserName, $ToUserName, $time, $msgType, $string);
    }

//音乐
    function returnMusic($FromUserName,$ToUserName,$title,$description,$url,$HQurl){
        $musicTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Music>
									<Title><![CDATA[%s]]></Title>
									<Description><![CDATA[%s]]></Description>
									<MusicUrl><![CDATA[%s]]></MusicUrl>
									<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
								</Music>
								</xml>";
        $time = time();
        $msgType = 'music';
        return sprintf($musicTpl, $FromUserName, $ToUserName, $time, $msgType,$title,$description,$url,$HQurl);
    }

    /**
     * 回复图文
     * $ArticleCount的格式为xml
     * <item>
     *<Title><![CDATA[%s]]></Title>
     *   <Description><![CDATA[%s]]></Description>
     *   <PicUrl><![CDATA[%s]]></PicUrl>
     *   <Url><![CDATA[%s]]></Url>
     *</item>
     */
    function returnNews($FromUserName,$ToUserName,$count,$ArticleCount){
        $newsTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<ArticleCount>%s</ArticleCount>
								%s
								</xml> ";
        $time = time();
        $msgType = 'news';
        return sprintf($newsTpl, $FromUserName, $ToUserName, $time, $msgType,$count, $ArticleCount);
    }

    /**
     * 回复图片/语音消息
     * $type 图片消息或则语音消息
     */
    function returnImage($FromUserName,$ToUserName,$type,$MediaId){
        $Tpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Image>
                                <MediaId><![CDATA[%s]]></MediaId>
                                </Image>
                                </xml>";
        $time = time();
        $msgType = $type;
        return sprintf($Tpl, $FromUserName, $ToUserName, $time, $msgType,$MediaId);
    }

    /**
     * 回复视频消息
     * @param $MediaId 视频的mediaID
     * @param $Title 视频标题
     * @param $Description  描述
     * @return string 返回拼接的xml
     */
    function returnVideo($FromUserName,$ToUserName,$MediaId,$Title,$Description){
        $videlTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Video>
                                <MediaId><![CDATA[%s]]></MediaId>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                </Video>
                                </xml>";
        $msgType = 'video';
        $time = time();
        return sprintf($videlTpl, $FromUserName, $ToUserName, $time, $msgType,$MediaId,$Title,$Description);
    }
}
