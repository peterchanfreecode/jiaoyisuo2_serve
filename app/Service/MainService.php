<?php


namespace App\Service;
use Illuminate\Support\Facades\Redis;
class MainService
{
    public static function main($key,$score)
    {
        if(Redis::ZRANGEBYSCORE($key, $score,$score)){
            $str = Redis::ZRANGEBYSCORE($key, $score,$score);
            $str = $str[0]??[];
            if($str){
                $arr = explode("_",$str);
                $num = $arr[1]??0;
                if($num<3){
                    Redis::ZREM($key,$str);
                    $val = $score.'_'.($num+1);
                    Redis::ZADD($key, $score,$val);
                    return true;
                }else{
                    return false;
                }
            }

        }else{
            $val = $score.'_1';
            Redis::ZADD($key, $score,$val);
            return true;
        }

    }

}