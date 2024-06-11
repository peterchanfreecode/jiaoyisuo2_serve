<?php
/**
 * User: LDH
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 系统设置项
 * @package App
 */
class AppSetting extends Model
{
    protected $table = 'app_settings';
    public $timestamps = false;
    protected $fillable = ['key', 'value'];

    public function getValueAttribute()
    {
        $value = $this->attributes['value'];

        if (stristr($value, 'upload')) {

            if (stristr($value, 'http')) {
                return $value;
            } else {
                return config('app.aws_url') . $value;
            }
        } else {
            return $value;
        }
    }
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
}