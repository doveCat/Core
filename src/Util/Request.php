<?php
namespace Interior\Petty\Util;

// 处理请求类型
class Request{

    public static $headers = [];
    public static $datas = [];
    public static $files = [];

    /**
     * @return string
     *
     * GetRequestMethod
     */
    public static function getRequestMethod(){
        $request_method = "unknown";
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        return $request_method;
    }

    /**
     * 初始化请求处理
     */
    public static function initialize(){
        PettyLog("Petty\Auth-initialize",'request');
        # 初始化请求处理
        if(static::getRequestMethod() == "options"){
            header('Access-Control-Allow-Origin:*');
            header('Access-Control-Allow-Headers:Origin, X-Requested-With,  Content-Type, Cookie, Accept, appid, channel, mbcore-access-token, mbcore-auth-token');
            header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, OPTIONS');
            header('Access-Control-Allow-Credentials:false');
            header('Access-Control-Max-Age:3600');
            echo "ok";
            exit;
        }
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                static::$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        $postdata = file_get_contents("php://input");
        if(empty($postdata)){
            $request = [];
        }else{
            $request = json_decode($postdata, true);  // php://input 只接收json类型
            if(empty($request)) $request = [];
        }
        $value =  array_merge(array_merge($request,$_GET),$_POST);
        static::$datas = $value;
    }


    /**
     * @param $key
     * @return bool|mixed
     *
     * GetHeader
     */
    public static function getHeader($key){
        $key = str_replace(' ', '-', ucwords(strtolower(str_replace('-', ' ',$key))));
        return static::$headers[$key]??false;
    }

//    public static function getFile($key) {
//        $value = static::$files[$key]??false;
//        return $value;
//    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     *
     * GetParam
     */
    public static function getParam($key,$default = null){
        $value = static::$datas[$key]??$default;
        return $value;
    }

    /**
     * @return array
     *
     * AllParam
     */
    public static function allParam(){
        $value = static::$datas;
        return $value;
    }


    /**
     * @return mixed|string
     *
     * GetClientIp
     */
    public static function getClientIp(){
        static $ip = '';
        $ip = $_SERVER['REMOTE_ADDR'];
        if(isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }
}