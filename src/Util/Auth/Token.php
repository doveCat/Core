<?php
namespace DACore\Util\Auth;

use DACore\Util\Auth\OAuth as DoveOAuth;
use DACore\Util\Request as DoveRequest;

class Token{

    private static $appid;
    private static $access_token_key;
    private static $access_token_value;

    private static $group_id = 1;
    private static $server_mbcore_client;
    private static $extra = [];

    /**
     * 初始化函数
     */
    public static function initialize()
    {
        # 初始化统一化处理请求
        DoveRequest::initialize();
        $appid = DoveRequest::getHeader("appid");
        DoveLog("Dove\Auth",'Token');
        DoveLog($appid,'Token');
        $access_token =  DoveRequest::getHeader("mbcore-access-token");
        DoveLog('$access_token:'.$access_token,'Token');
        if (!$appid  || !$access_token) {
            DoveResponseAuthErr("Headers AccessToken does not exist!",403.11);
        }
        DoveLog($access_token,'Token');
        self::$appid = $appid;
        self::$access_token_key = $access_token;
        $data = static::getAccessToken();
        DoveLog('data-appid:'.$data['appid'],'Token');
        DoveLog('appid-appid:'.$appid,'Token');
        if($data == false){
            DoveResponseAuthErr("access-token Err!",403.12);
        }
        if($appid != $data['appid']){
            DoveResponseAuthErr("AppID Err!",403.13);
        }
        $timestamp = intval($data['expires_time']);
        if (time() - $timestamp > 0) {
            DoveResponseAuthErr("access_token expires time Err!",403.14);
        }
        self::$group_id = $data['group_id'];
        self::$server_mbcore_client = $data['client'];
        # 设置额外参数
        if(isset($data['extra'])){
            self::$extra = $data['extra'];
        }
    }


    /**
     * @return bool|mixed
     *
     * Get Access Token
     */
    private static function getAccessToken(){
        $access_token_value = false;
        //$database_redis = DoveConfig("redis.database_redis")??"10"; // 默认使用10号库
        //DoveRedis::selectDB($database_redis);
        $access_token_value = \DoveRedisAuth::get('oauth_access_token:' .self::$access_token_key);
        //DoveDebug('oauth_access_token:' .self::$access_token_key);
        //DoveDebug($access_token_value);
        //DoveRedis::defaultDB();
        //$access_token_value = unserialize($access_token_value);  //serialize 序列化与反序列化
        self::$access_token_value = $access_token_value;
        DoveLog('getAccessToken'.json_encode($access_token_value),'token');
        return $access_token_value;
    }


    /**
     * @return mixed
     *
     * Get GroupID
     */
    public static function getGroupId(){
        return self::$group_id;
    }


    /**
     * @return mixed
     *
     * Get GroupID
     */
    public static function getAppId(){
        return self::$appid;
    }


    /**
     * @return mixed
     *
     * Get Server MBCore Client
     */
    public static function getServerMBCoreClient(){
        return self::$server_mbcore_client;
    }

    /**
     * @param string $str
     * @return bool|mixed
     */
    public static function getExtraParam($str = ""){
        if( $str == "" ) return false;
        if(isset(self::$extra[$str])){
            return self::$extra[$str];
        }
        return false;
    }


    /**
     * @return array
     *
     * 获取用户信息
     */
    public static function getUserInfo(){
        $auth_token = DoveRequest::getHeader("mbcore-auth-token");
        if (!$auth_token) {
            DoveResponseAuthErr("Headers AuthToken does not exist!",403.21);
        }
        // 验证用户登录信息
        $userData =\DoveRedisAuth::get('oauth_auth_token:' . $auth_token);
        //$userData = unserialize($userData);
        //return $userData;
        if ($userData == false) {
            DoveResponseAuthErr("auth-token Err!",403.22);
        }

        if(self::$access_token_key != $userData['access_token']){
            DoveResponseAuthErr("Access_token don't match!",403.23);
        }
        // auth_token
        $timestamp2 = intval($userData['expires_time']);
        if (time() - $timestamp2 > 0) {
            DoveResponseAuthErr("auth-token expires time Err!",403.24);
        }

        $user_id = $userData['user_id'];
        $user_type = $userData['user_type'];

        $userInfo = [
            'user_id'=>$user_id,
            'user_type'=>$user_type,
            'token' => $userData
        ];

        if (isset($userData['more']) && count($userData['more']) > 0) {
            $userInfo['more'] = $userData['more'];
        }

        return $userInfo;
    }

    /**
     * @return array|bool
     *
     * 检测用户登录
     */
    public static function checkUserLogin(){
        $auth_token = DoveRequest::getHeader("mbcore-auth-token");
        if ($auth_token) {
            $userData =\DoveRedisAuth::get('oauth_auth_token:' . $auth_token);
            //$userData = unserialize($userData);
            if ($userData) {
                $timestamp2 = intval($userData['expires_time']);
                if(time() - $timestamp2 < 0){
                    $user_id = $userData['user_id'];
                    $user_type = $userData['user_type'];
                    return [
                        'user_id'=>$user_id,
                        'user_type'=>$user_type,
                        'token' => $userData
                    ];
                }
            }
        }
        return false; //未登录
    }

    /**
     * @param bool $check
     * @return bool|mixed
     */
    public static function authInfo($check=true){
        $auth_token_key = DoveOAuth::getAuthTokenKey();
        $AuthData = \DoveRedisAuth::get('oauth_auth_token:' . $auth_token_key);
        $access_token =  DoveRequest::getHeader("mbcore-access-token");
        if($check){
            # 需要验证时的逻辑
            DoveRequest::initialize();
            $auth_token = DoveRequest::getHeader("mbcore-auth-token");
            if (!$auth_token || !$AuthData) {
                DoveResponseAuthErr("Headers AuthToken does not exist!",403.21);
            }

            if ($AuthData["auth_token"] != $auth_token) {
                DoveResponseAuthErr("auth-token Err!",403.22);
            }
//            DoveLog("***authInfo[1]*****");
//            DoveLog($access_token);
//            DoveLog($AuthData['access_token']);
//            DoveLog($AuthData);
//            DoveLog("***authInfo[2]*****");
            if($access_token != $AuthData['access_token']){
                DoveResponseAuthErr("Access_token don't match!",403.23);
            }
            if (time() - intval($AuthData['expires_time']) > 0) {
                DoveResponseAuthErr("auth-token expires time Err!",403.24);
            }
        }
        if($AuthData){
            unset($AuthData['expires_time'],$AuthData['access_token'],$AuthData['auth_token']);
            return $AuthData;
        }else{
            return false; # 未登录
        }
    }
}