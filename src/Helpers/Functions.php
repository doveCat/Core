<?php

namespace DACore\Core\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use DACore\Core\Models\Partner;
use Illuminate\Support\Carbon;

class Functions {

    /**
     * @param $tel
     * @param bool $onlyMob
     * @return array
     *
     * 电话号验证
     */
    public static function DCTelVerify($tel,$onlyMob = false){
        $isMob = "/^1[3-9]{1}[0-9]{9}$/";
        $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        $special = '/^(4|8)00(-\d{3,4}){2}$/';//'/^(4|8)00(\d{4,8})$/';
        $data3 = substr($tel, 0,3);
        $data2 = substr($tel, 0,2);
        $msg = 'success';
        $msg_zh = '成功';
        $code = 1;
        if($onlyMob){# 只验证手机号，不验证座机和400|800的号码
            if (preg_match($isMob, $tel)) {
                if($data2 == 14){
                    if(!in_array($data3,[147,145])){
                        # 只开放 147,145
                        $msg = $data3.' is not open!';
                        $msg_zh = $data3.' 号段暂未开放！';
                        $code = 0;
                        return [
                            'code' => $code,
                            'msg'=>$msg,
                            'msg_zh' => $msg_zh
                        ];
                    }
                } else if($data2 == 16){
                    if(!in_array($data3,[165,166,167])){
                        # 只开放 165,166,167
                        $msg = $data3.' is not open';
                        $msg_zh = $data3.' 号段暂未开放！';
                        $code = 0;
                        return [
                            'code' => $code,
                            'msg'=>$msg,
                            'msg_zh' => $msg_zh
                        ];
                    }
                }else if($data2 == 17){
                    if(in_array($data3,[179,174])){
                        # 未开放 179,174
                        $msg = $data3.' is not open';
                        $msg_zh = $data3.' 号段暂未开放！';
                        $code = 0;
                        return [
                            'code' => $code,
                            'msg'=>$msg,
                            'msg_zh' => $msg_zh
                        ];
                    }
                }else if($data2 == 19){
                    if(!in_array($data3,[199,198])){
                        # 只开放 199,198
                        $msg = $data3.' is not open';
                        $msg_zh = $data3.' 号段暂未开放！';
                        $code = 0;
                        return [
                            'code' => $code,
                            'msg'=>$msg,
                            'msg_zh' => $msg_zh
                        ];
                    }
                }
            } else {
                $msg = 'Invalid mobile phone number';
                $msg_zh = '手机号不合法！';
                $code = 0;
                return [
                    'code' => $code,
                    'msg' => $msg,
                    'msg_zh' => $msg_zh
                ];
            }
            return [
                'code' => $code,
                'msg' => $msg,
                'msg_zh' => $msg_zh
            ];
        }else {# 手机、座机、以及400|800号码的验证
            if (preg_match($isMob, $tel)) {
                if($data2 == 14){
                    if(!in_array($data3,[147,145])){
                        # 只开放 147,145
                        $msg = $data3.' is not open!';
                        $msg_zh = $data3.' 号段暂未开放！';
                        $code = 0;
                        return [
                            'code' => $code,
                            'msg'=>$msg,
                            'msg_zh' => $msg_zh
                        ];
                    }
                }else if($data2 == 16){
                    if(!in_array($data3,[165,166,167])){
                        # 只开放 165,166,167
                        $msg = $data3.' is not open';
                        $msg_zh = $data3.' 号段暂未开放！';
                        $code = 0;
                        return [
                            'code' => $code,
                            'msg'=>$msg,
                            'msg_zh' => $msg_zh
                        ];
                    }
                }else if($data2 == 17){
                    if(in_array($data3,[179,174])){
                        # 未开放 179,174
                        $msg = $data3.' is not open';
                        $msg_zh = $data3.' 号段暂未开放！';
                        $code = 0;
                        return [
                            'code' => $code,
                            'msg'=>$msg,
                            'msg_zh' => $msg_zh
                        ];
                    }
                }else if($data2 == 19){
                    if(!in_array($data3,[199,198])){
                        # 只开放 199,198
                        $msg = $data3.' is not open';
                        $msg_zh = $data3.' 号段暂未开放！';
                        $code = 0;
                        return [
                            'code' => $code,
                            'msg'=>$msg,
                            'msg_zh' => $msg_zh
                        ];
                    }
                }
            } else if (preg_match($special, $tel)) {
                return [
                    'code' => $code,
                    'msg' => $msg,
                    'msg_zh' => $msg_zh
                ];
            } else if (preg_match($isTel, $tel)){
                return [
                    'code' => $code,
                    'msg' => $msg,
                    'msg_zh' => $msg_zh
                ];
            } else {
                $msg = 'Invalid mobile phone number,If it is a fixed telephone, it must be like (010-87876787 or 400-000-0000)!';
                $msg_zh = '手机号不合法,如果是固定电话, 必须类似以下号码 (010-87876787 或者 400-000-0000)！';
                $code = 0;

                return [
                    'code' => $code,
                    'msg' => $msg,
                    'msg_zh' => $msg_zh
                ];
            }
            return [
                'code' => $code,
                'msg' => $msg,
                'msg_zh' => $msg_zh
            ];
        }
    }

    /**
     * @param int $num | 位数
     * @return string
     *
     * 生成随机邀请码
     */
    public static function DCGenInvitationCode($num = 6){
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            $code = '',
            $f = 0;
            $f < $num;
            $g = ord( $a[ $f ] ),
            $code .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        return $code;
    }
    // 生成随机字符串
    public static function DCRandom($length = 16)
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
     *
     * 生成Token
     */
    public static function DCGetToken()
    {
        $token = hash_hmac('sha1',static::MPRandom(1000),static::MPRandom(100));
        return $token;
    }

    /**
     * @param $access_token_key
     * @return bool
     *
     * 获取AuthTokenKey
     */
    public static function DCGetAuthTokenKey($access_token_key){
        \Log::notice('获取AuthTokenKey--$access_token_key:'.$access_token_key);
        $cacheKey = 'oauth_access_token:' . $access_token_key;
        \Log::notice('获取AuthTokenKey--$cacheKey:'.$cacheKey);
        $access_token = \Cache::get($cacheKey);
        \Log::notice('获取AuthTokenKey--$access_token:'.json_encode($access_token));
        if($access_token){
            return $access_token["client"];
        }else{
            return false;
        }
    }

    /**
     * @return string
     *
     * 生成唯一订单通信编码
     */
    public static function DCGenHash(){
        $hash = md5(uniqid() . print_r($_SERVER, 1) . microtime(1) . openssl_random_pseudo_bytes(1000));
        return $hash;
    }

    /**
     * 简单加密
     * @param mixed $string 要加密的内容
     * @param $key 密钥
     * @return string
     */
    public static function DCEncrypt($string, $key)
    {
//    $iv = openssl_random_pseudo_bytes (16);
        $data = openssl_encrypt(serialize($string),'rc4',$key,1,null);
        return strtr(base64_encode($data),[
            '=' => null,
            '/' => '_',
            '+' => '-'
        ]);
    }

    /**
     * 简单解密
     * @param $string 要解密的内容
     * @param $key 密钥
     * @return mixed|string
     */
    public static function DCDecrypt($string,$key)
    {
        $data = base64_decode(strtr($string,[
            '_' => '/',
            '-' => '+'
        ]));
//    $iv = substr($data,0,16);
        $data = @unserialize(openssl_decrypt($data,'rc4',$key,1,null));
        return $data;
    }


    /**
     * @param $users
     * @param $profit
     * @param $group_id
     * @param $profit_reason
     * @param int $store_id
     * @return bool
     *
     * 合伙人收益结算
     */
    public static function  DCProfitSettlement($users,$profit,$group_id,$profit_reason,$store_id = 0)
    {
        if ($users) {
            foreach ($users as $key => $val) {
                # 如果用户有合伙人，合伙人获取加对应的收益
                $where_invited_user = [
                    'group_id' => $group_id,
                    'store_id' => $store_id,
                    'user_id' => $val,
                ];
                $invited_user = PartnerInvite::query()->where('user_id', $val)->first();
                if($invited_user){
                    $partner = Partner::query()->find($invited_user['partner_id']);
                    $partner->increment('total_income',$profit);
                    $partner->increment('income',$profit);
                    # 合伙人收益记录
                    PartnerIncomeLog::query()->create([
                        'store_id' => $store_id,
                        'group_id' => $group_id,
                        'user_id' => $val,
                        'income' => $profit,
                        'income_source' => $profit_reason,
                        'partner_id'=>$invited_user['partner_id']
                    ]);
                }else{
                    continue;
                }
            }
        }
        return true;
    }

    public static function DCSendSMSVerifyCode($phone, $msg_type, $type, $group_id) {
        $cacheKey = 'getCode'.$type.':' . md5('SMS'.$phone.'-'.$group_id.'-'.$type);
        $code = \Cache::get($cacheKey);
        \Log::notice('sendSMS--'.$cacheKey."--code--".$code);
        if (!$code) {
            $randStr = str_shuffle('1234567890');
            $code = substr($randStr,0,6);
            # 10分钟
            $expiresAt = Carbon::now()->addMinutes(10);
            \Cache::put($cacheKey, $code, $expiresAt);
        }
        $vars = [
            'code' => $code,
            'time' =>  10,
        ];
        $where_config = [
            'group_id'=>$group_id
        ];
        $config = SMSConfig::query()->where($where_config)->first();
        if(!$config){
            \Log::notice('SMS验证码发送--SMS配置异常');
            return [
                'code'=>0,
                'msg'=>'系统异常'
            ];
        }
        $appid = $config['appid'];
        $appkey = $config['appkey'];
        $where_type = [
            'group_id'=>$group_id,
            'msg_tag'=>$msg_type,
            'is_used'=>1
        ];
        $type = SMSMsgType::query()->where($where_type)->first();
        if(!$type){
            \Log::notice('SMS验证码发送--短信消息主体配置异常');
            return [
                'code'=>0,
                'msg'=>'系统异常'
            ];
        }
        $templates_id = $type['template_id'];
        $param = [
            'appid' => $appid,
            'signature' => $appkey,
            'project' => $templates_id,
            'to' => $phone,
            'vars' => json_encode($vars),
        ];
        $url = 'https://api.mysubmail.com/message/xsend.json';
        $client = new Client(['verify'=> false]);
        $response = $client->request('post', $url,[
            'form_params'=>$param,
            'timeout'=>10
        ]);
        $data = $response->getBody()->getContents();
        $result = json_decode($data,true);
        if($result['status'] == 'success'){
            $time = 60;
            $return = [
                'time'=>$time
            ];
            return ['code'=>1,'return'=>$return];
        }else{
            return [
                'code'=>0,
                'msg'=>'系统异常，请重新获取'
            ];
        }
    }
}