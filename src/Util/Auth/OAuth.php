<?php
namespace Interior\Petty\Util;

use Interior\Petty\Util\Request as PettyRequest;

# 处理请求类型
class OAuth{

    /**
     * @param $appid
     * @param $roles
     * @param array $request_all
     * @return string
     */
    public static function getSign($appid,$roles,$request_all=[]){
        if(empty($appid) || !is_array($roles)) {
            return '';
        }
        unset($roles['extra']); # 排除额外信息参与签名
        if(empty($request_all)){
            $request_all = PettyRequest::allParam(); # 无论是post还是get方式提交都可以用$_REQUEST
        }
        $timestamp = intval($request_all['timestamp']/1000);  # 使用秒级时间戳，传递时为毫秒时间戳
        unset($request_all['sign'],$request_all['timestamp']); # 去掉校验本身,时间戳
        $arr = array_merge($request_all,['appid'=>$appid,'timestamp'=>$timestamp],$roles);
        # 按照首字母大小写顺序排序
        sort($arr,SORT_STRING); # SORT_STRING - 把每一项作为字符串来处理。
        # 拼接成字符串
        $str = implode($arr);
        # 进行加密
        $signature = sha1($str);
        $signature = md5($signature);
        # 转换成大写
        $signature = strtoupper($signature);
        return $signature;
    }


    /**
     * @param int $length
     * @return string
     * @throws \Exception
     *
     * 生成随机字符串
     */
    public static function random($length = 16)
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    /**
     * @return string
     * @throws \Exception
     *
     * 生成Token
     */
    public static function getToken()
    {
        return hash_hmac('sha1',static::random(1000),static::random(100));
    }


    /**
     * @return bool
     *
     * 获取AuthTokenKey
     */
    public static function getAuthTokenKey(){

        PettyRequest::initialize();
        $access_token_key = PettyRequest::getHeader("mbcore-access-token");
        PettyLog("access_token_key:::".$access_token_key);
        $access_token_val = \PettyRedisAuth::get('oauth_access_token:' . $access_token_key);
        PettyLog("access_token_val:::".json_encode($access_token_val));
        if($access_token_val){
            return $access_token_val["client"];
        }else{
            return false;
        }
    }

}