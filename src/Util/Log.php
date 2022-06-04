<?php
namespace DACore\Util;

class Log{
    /**
     * @param $str
     * @return string
     */
    public static function Info($str){
        $info = "";
        $info .= "\n------------------------------------------\n";
        $info .= "------------------------------------------\n";
        $info .= "  [***(Time:".date("Y-m-d H:i:s", time()).")***]\n";
        $info .= $str;
        $info .= "\n***************************************** \n";
        $info .= "***************************************** \n";
        return $info;
    }

    /**
     * @param $str
     * @return string
     */
    public static function String($str){
        $info = "";
        $info .= "      ".trim($str,"\n");
        return $info;
    }

    /**
     * @param $data
     * @return string
     */
    public static function Exception($data){
        $info = "";
        $info .= "      [  code ]".$data['code']."\n";
        $info .= "      [message]".$data['message']."\n";
        $info .= "      [  file  ]".$data['file']."\n";
        $info .= "      [  line  ]".$data['line']."\n";
        if(isset($data['data'])){
            $info .= "      [  data  ](".gettype($data['data']).")\n";
            $info .= "          [  **data-value**  ]\n\n";
            $info .= print_r($data['data'],true)."\n";
        }
        return $info;
    }
}