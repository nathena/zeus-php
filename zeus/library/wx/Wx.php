<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 15/8/5
 * Time: 11:43
 */

namespace wx;

class Wx
{
    private static $access_token_api = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
    private static $jsapi_ticket_api = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";

    private static $site_access_code_state = "zeus-wx-gateway";

    private static $site_access_code_api = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect";
    private static $openid_access_token_api = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";
    private static $refresh_token_api = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s";
    private static $userinfo_api = "https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=%s";
    private static $check_openid = "https://api.weixin.qq.com/sns/auth?access_token=%s&openid=%s";

    private static $snsapi_base = 'snsapi_base';
    private static $snsapi_userinfo = 'snsapi_userinfo';

    private static $zh_CN = 'zh_CN';
    private static $zh_TW = 'zh_TW';
    private static $en = 'en';

    protected $wxAppID;
    protected $wxAppSecret;

    protected $sapiType;

    protected $token = array();
    protected $jsapiTicketData = array();

    public function __construct($wxAppID,$wxAppSecret,$is_userinfo_sapi=true)
    {
        $this->wxAppID = $wxAppID;
        $this->wxAppSecret = $wxAppSecret;
        $this->sapiType = $is_userinfo_sapi ? self::$snsapi_userinfo : self::$snsapi_base;
    }

    public function jsapiSignature($url)
    {
        $noncestr = generateUUID();
        $timestamp = time();

        $jsapiTicketData = $this->jsapiTicket();

        $signatureData = array();
        $signatureData["noncestr"] = $noncestr;
        $signatureData["timestamp"] = $timestamp;
        $signatureData["url"] = $url;
        $signatureData["jsapi_ticket"] = $jsapiTicketData["ticket"];

        ksort($signatureData);

        $signatureStr = urldecode(http_build_query($signatureData));

        $signature = sha1($signatureStr);

        $signatureData["signature"] = $signature;
        $signatureData["appId"] = $this->wxAppID;

        return $signatureData;
    }

    public function wxAccessToken($redirectUri)
    {
        $api = sprintf(self::$site_access_code_api,$this->wxAppID,rawurlencode($redirectUri),$this->sapiType,self::$site_access_code_state);

        redirect($api);
    }

    public function wxLoadOpenidByCode($code)
    {
        if(empty($code))
        {
            throw new ZeusException("微信openid code is empty");
        }

        $api = sprintf(self::$openid_access_token_api,$this->wxAppID,$this->wxAppSecret,$code);

        $response = HttpReq::get(array("url"=>$api));

        $wxOpenid = json_decode($response,true);
        if( $wxOpenid["errcode"] )
        {
            throw new ZeusException($wxOpenid["errmsg"],$wxOpenid["errcode"]);
        }

        if( $this->sapiType == self::$snsapi_userinfo )
        {
            $wxOpenid = $this->userInfo($wxOpenid);
        }

        return $wxOpenid;
    }

    protected function accessToken()
    {
        if( empty($this->token) || ( $this->token["create_time"] &&  ( $this->token["create_time"] + $this->token["expires_in"] <= time() )  ))
        {

            $api = sprintf(self::$access_token_api,$this->wxAppID,$this->wxAppSecret);
            $response = HttpReq::get(array("url"=>$api));

            $data = json_decode($response,true);
            if( $data["errcode"] )
            {
                throw new ZeusException($data["errmsg"],$data["errcode"]);
            }

            $data["create_time"] = time();
            $this->token = $data;
        }

        return $this->token;
    }

    protected function jsapiTicket()
    {
        if( empty($this->jsapiTicketData) || ( $this->token["create_time"] &&  ( $this->token["create_time"] + $this->token["expires_in"] <= time() ) ) )
        {
            $token = $this->accessToken();

            $api = sprintf(self::$jsapi_ticket_api,$token["access_token"]);

            $response = HttpReq::get(array("url"=>$api));

            $data = json_decode($response,true);
            if( $data["errcode"] )
            {
                throw new ZeusException($data["errmsg"],$data["errcode"]);
            }

            $data["create_time"] = time();
            $this->jsapiTicketData = $data;
        }

        return $this->jsapiTicketData;
    }

    protected function userInfo($wxOpenid)
    {
        $api = sprintf(self::$userinfo_api,$wxOpenid["access_token"],$wxOpenid["openid"],self::$zh_CN);

        $response = HttpReq::get(array("url"=>$api));
        $data = json_decode($response,true);

        $wxOpenid["lang"] = self::$zh_CN;

        foreach($data as $key => $val )
        {
            $wxOpenid[$key] = $val;
        }

        if( 2 == $wxOpenid["sex"] ){
            $wxOpenid["sex"] = "female";
        }
        if( 1 == $wxOpenid["sex"] ){
            $wxOpenid["sex"] = "male";
        }

        return $wxOpenid;
    }
}