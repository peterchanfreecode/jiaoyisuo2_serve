<?php

namespace App\Http\Controllers\Api;

use App\Models\Token;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Config;
use App;

class Controller extends BaseController
{
    public $user_id;
    public $language = 'en';

    public function __construct($_init = true)
    {

        if ($_init) {
            $token = Token::getToken();
            $this->user_id = Token::getUserIdByToken($token);
        }

        if (\Request::header('lang')) {
            $this->language = \Request::header('lang');
        }
    }


    /**
     * 返回一个错误响应
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message)
    {
        $lang_arr = ['de' => 'de', 'zh' => 'zh', 'fra' => 'fra', 'kr' => 'kr', 'th' => 'th', 'vi' => 'vi', 'jp' => 'jp', 'en' => 'en', 'hk' => 'hk'];
        $lang = key_exists($this->language, $lang_arr) ? $lang_arr[$this->language] : 'en';
        header('Content-Type:application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,lang');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        if (is_string($message)) {
            App::setlocale($lang);
            $message = str_replace('massage.', '', __("massage.$message"));
            $message = mtranslate($message, $lang);
        }
        return response()->json(['type' => 'error', 'message' => $message]);
    }

    public function lang($key, $vars = [])
    {
        $tip = Config::get('tips.' . $this->language . '.' . $key);
        $con = ($tip ?? Config::get('tips.' . Config::get('tips.default') . '.' . $key)) ?? 'system error';
        foreach ($vars as $var => $val) {
            $con = str_replace('{' . $var . '}', $val, $con);
        }
        return $con;
    }

    /**
     * 返回一个成功响应
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($message, $type = 0)
    {
        $lang_arr = ['de' => 'de', 'zh' => 'zh', 'fra' => 'fra', 'kr' => 'kr', 'th' => 'th', 'vi' => 'vi', 'jp' => 'jp', 'en' => 'en', 'hk' => 'hk'];
        $lang = key_exists($this->language, $lang_arr) ? $lang_arr[$this->language] : 'en';
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
        return response()->json(['type' => 'ok', 'message' => $message]);
    }

    /**
     * 返回一个成功响应
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function success_ceshi($message)
    {
        header('Content-Type:application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        if (is_string($message)) {
            $message = str_replace('massage.', '', __("massage.$message"));
        }
        return response()->json(['type' => 'ok', 'message' => $message]);
    }


    public function pageData($paginateObj)
    {
        $results = [
            'data' => $paginateObj->items(),
            'page' => $paginateObj->currentPage(),
            'pages' => $paginateObj->lastPage(),
            'total' => $paginateObj->total()
        ];
        return $this->success($results);
    }

    public function returnStr($str)
    {
        $message = str_replace('massage.', '', __("massage.$str"));
        return $message;
    }
}
