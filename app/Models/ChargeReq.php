<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeReq extends Model
{
    //
    protected $table = 'charge_req';
    protected $appends = [
        'currency_name',//币种
        'account_number',
        'account'
    ];

    public function getCurrencyNameAttribute()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id')->value('name');
    }
    public function getAccountNumberAttribute()
    {
        return $this->hasOne(Users::class, 'id', 'uid')->value('account_number');
    }

    public function getAccountAttribute()
    {
        $value = $this->hasOne(Users::class, 'id', 'uid')->value('phone');
        if (empty($value)) {
            $value = $this->hasOne(Users::class, 'id', 'uid')->value('email');
        }
        return $value;
    }
    public function getChangeUrlAttribute()
    {
        $value = $this->attributes['charge_url'];
        if (stristr($value, 'http')) {
            return $value;
        } else {
            return config('app.aws_url'). $value;
        }
    }
    public function user()
    {
        return $this->belongsTo(Users::class, 'uid', 'id');
    }
    public static function get_lists($list)
    {
        if ($list) {
            foreach ($list as $v) {
                $user_str = '<span>用户ID: ' . $v->uid . "</span>" . "<br>";
                $user_str .= '<span>用户名: ' . $v->account_number . "</span>" . "<br>";
                $user_str .= '<span>所属代理: ' . $v->belong_agent_name . "</span>" . "<br>";
                $user_str .= '<span>充币时间: ' . $v->created_at . "</span>" . "<br>";
                $v["time_info"] = $user_str;
                if (!stristr($v["charge_url"], 'http')) {
                    $v["charge_url"]  = config('app.aws_url'). $v["charge_url"];
                }
            }
        }
        return $list;
    }
    public static function get_en_lists($list)
    {
        if ($list) {
            foreach ($list as $v) {
                $user_str = '<span>User ID: ' . $v->uid . "</span>" . "<br>";
                $user_str .= '<span>username: ' . $v->account_number . "</span>" . "<br>";
                $user_str .= '<span>Agent: ' . $v->belong_agent_name . "</span>" . "<br>";
                $user_str .= '<span>Deposit time: ' . $v->created_at . "</span>" . "<br>";
                $v["time_info"] = $user_str;
                if (!stristr($v["charge_url"], 'http')) {
                    $v["charge_url"]  = config('app.aws_url'). $v["charge_url"];
                }
            }
        }
        return $list;
    }
}
