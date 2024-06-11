<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FeedBack extends Model
{
    protected $table = 'feedback';
    public $timestamps = false;
    protected $appends = ['account_number'];

    public function getCreateTimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['create_time']);
    }
    public function getReplyTimeAttribute()
    {
        if($this->attributes['reply_time']){
            return date('Y-m-d H:i:s', $this->attributes['reply_time']);
        }
        
    }
    public static function getNameById($currency_id)
    {
        $currency = self::find($currency_id);
        return $currency->name;
    }
    public function getAccountNumberAttribute()
    {
        return $this->hasOne(Users::class, 'id', 'user_id')->value('account_number');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }



}
