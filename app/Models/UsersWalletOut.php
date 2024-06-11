<?php

/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsersWalletOut extends Model
{
    protected $table = 'users_wallet_out';
    public $timestamps = false;
    protected $appends = ['currency_name', 'account_number','gold','gold_en', 'currency_type',"real_type"];
    //节点等级
    const TO_BE_AUDITED = 1;
    const ToO_EXAMINE_ADOPT = 2;
    const ToO_EXAMINE_FAIL = 3;

    public function getCurrencyNameAttribute()
    {
        return $this->hasOne(Currency::class, 'id', 'currency')->value('name');
    }

    public function getCurrencyTypeAttribute()
    {
        return $this->hasOne(Currency::class, 'id', 'currency')->value('type');
    }

    public function getAccountNumberAttribute()
    {
        return $this->hasOne(Users::class, 'id', 'user_id')->value('account_number');
    }

    public function getCreateTimeAttribute()
    {
        $value = $this->attributes['create_time'];
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }
    public function getUpdateTimeAttribute()
    {
        $value = $this->attributes['update_time'];
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id')->withDefault();
    }
    public function getGoldAttribute()
    {
        $user_id = $this->attributes['user_id'];
        $info = UserGold::where("user_id",$user_id)->first();
        if(!$info){
            return "已扣除";
        }
       return $info->status == 1? "已扣除":"未扣除";
    }
    public function getGoldEnAttribute()
    {
        $user_id = $this->attributes['user_id'];
        $info = UserGold::where("user_id",$user_id)->first();
        if(!$info){
            return "deducted";
        }
        return $info->status == 1? "deducted":"not deducted";
    }
    public static function get_lists($list)
    {
        if ($list) {
            foreach ($list as $v) {
                $user_str = '<span>用户名: ' . $v->account_number . "</span>" . "<br>";
                $user_str .= '<span>所属代理: ' . $v->belong_agent_name . "</span>" . "<br>";
                $user_str .= '<span>申请时间: ' . $v->create_time . "</span>" . "<br>";
                $user_str .= '<span>通过时间: ' . $v->update_time . "</span>" . "<br>";
                $v["time_info"] = $user_str;
            }
        }
        return $list;
    }
    public static function get_en_lists($list)
    {
        if ($list) {
            foreach ($list as $v) {
                $user_str = '<span>username: ' . $v->account_number . "</span>" . "<br>";
                $user_str .= '<span>Agent: ' . $v->belong_agent_name . "</span>" . "<br>";
                $user_str .= '<span>application time: ' . $v->create_time . "</span>" . "<br>";
                $user_str .= '<span>passing time: ' . $v->update_time . "</span>" . "<br>";
                $v["time_info"] = $user_str;
            }
        }
        return $list;
    }
    public function getRealTypeAttribute()
    {
        $value = $this->attributes['is_real']??'';
        if($value == 1 ){
            return  "真实提币";
        }else if($value == 2){
            return  "试完提币";
        }else{
            return  "---";
        }
    }
}
