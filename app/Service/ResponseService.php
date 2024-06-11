<?php
namespace App\Service;
use App;
class ResponseService
{
    public static function success($message,$status, $type = 0)
    {
        $lang_arr = ['kr' => 'kor', 'hk' => 'cht', 'jp' => 'jp', 'en' => 'en', 'zh' => 'zh'];
        $lang = key_exists( 'en', $lang_arr) ? $lang_arr[ 'en'] : 'en';
        header('Content-Type:application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        if (is_string($message) && $type == 0) {
            App::setlocale($lang);
            $message = str_replace('massage.', '', __("massage.$message"));
            $message = mtranslate($message, $lang);
        }
        return response()->json(['type' => $status, 'message' => $message]);
    }
}