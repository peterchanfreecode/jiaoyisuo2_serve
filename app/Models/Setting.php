<?php
/**
 * User: LDH
 */

namespace App\Models;

use App\Utils\RPC;
use Illuminate\Database\Eloquent\Model;

/**
 * 系统设置项
 * @package App
 */
class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = false;
    protected $fillable = ['key', 'value'];

    public static function getValueByKey($key = "", $defalut = "")
    {
        if (empty($key))
            return $defalut;
        $settings = self::where('key', $key)->orderBy('id', 'DESC')->first();
        if (empty($settings)) {
            return $defalut;
        } else {
            return $settings->value;
        }
    }

    public static function updateValueByKey($key = "", $value = "")
    {
        if (empty($key))
            return false;
        $settings = self::where('key', $key)->orderBy('id', 'DESC')->first();
        if (empty($settings)) {
            $setting = new self();
        } else {
            $setting = self::find($settings->id);
        }
        $setting->key = $key;
        $setting->value = $value;
        $setting->save();
        return true;
    }

    public static function getValueByExplode($key = "")
    {
        if (empty($key))
            return "";
        $settings = self::where('key', $key)->orderBy('id', 'DESC')->first();
        if (empty($settings)) {
            return "";
        } else {
            $settings = explode("|", $settings->value);
            return $settings;
        }
    }

    public static function sendSmsForSmsBao($mobile, $content)
    {
        try {
            $username = self::getValueByKey('smsBao_username', '');
            $password = self::getValueByKey('password', '=');
            if (empty($mobile)) {
                throw new \Exception('请填写手机号');
            }

            if (empty($content)) {
                throw new \Exception('请填写发送内容');
            }
            $format_content = '【MTS】';
            $api = 'http://api.smsbao.com/sms';
            $send_url = $api . "?u=" . $username . "&p=" . md5($password) . "&m=" . $mobile . "&c=" . urlencode($format_content . $content);
            $return_message = RPC::apihttp($send_url);
            if ($return_message == 0) {
                return true;
            } else {
                $statusStr = array(
                    "-1" => "参数不全",
                    "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
                    "30" => "密码错误",
                    "40" => "账号不存在",
                    "41" => "余额不足",
                    "42" => "帐户已过期",
                    "43" => "IP地址限制",
                    "50" => "内容含有敏感词"
                );
                throw new \Exception($statusStr[$return_message]);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

    }

    public static function get_level($real_teamnumber, $candy_number)
    {
        $level = 0;
        $one_level_num = self::getValueByKey("one_level_rebate_lower_num");
        $one_level_lock = self::getValueByKey("one_level_rebate_lock");
        $two_level_num = self::getValueByKey("two_level_rebate_lower_num");
        $two_level_lock = self::getValueByKey("two_level_rebate_lock");
        $three_level_num = self::getValueByKey("three_level_rebate_lower_num");
        $three_level_lock = self::getValueByKey("three_level_rebate_lock");


        if ($one_level_num <= $real_teamnumber && $one_level_lock <= $candy_number) {
            $level = 1;
        }
        if ($two_level_num <= $real_teamnumber && $two_level_lock <= $candy_number) {
            $level = 2;
        }
        if ($three_level_num <= $real_teamnumber && $three_level_lock <= $candy_number) {
            $level = 3;
        }
        return $level;
    }

    public static function get_currery($real_teamnumber, $new_currery_number)
    {
        $rate = 0;
        $one_level_num = self::getValueByKey("one_level_lottery_lower_num");
        $one_level_lock = self::getValueByKey("one_level_lottery_currery");
        $two_level_num = self::getValueByKey("two_level_lottery_lower_num");
        $two_level_lock = self::getValueByKey("two_level_lottery_currery");
        $three_level_num = self::getValueByKey("three_level_lottery_lower_num");
        $three_level_lock = self::getValueByKey("three_level_lottery_currery");
        if ($one_level_num <= $real_teamnumber && $one_level_lock <= $new_currery_number) {
            $rate = self::getValueByKey("one_level_lottery_rate");
        }
        if ($two_level_num <= $real_teamnumber && $two_level_lock <= $new_currery_number) {
            $rate = self::getValueByKey("two_level_lottery_rate");
        }
        if ($three_level_num <= $real_teamnumber && $three_level_lock <= $new_currery_number) {
            $rate = self::getValueByKey("three_level_lottery_rate");
        }
        return $rate;
    }
}