<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashAgainst extends Model
{
    protected $table = 'flash_against';
    public $timestamps = false;
    protected $appends = ['status_name','mobile','l_currency','r_currency'];

    public function getMobileAttribute()
    {
        return $this->hasOne(Users::class, 'id', 'user_id')->value('account_number');
    }
    public function getLCurrencyAttribute()
    {
        return $this->hasOne(Currency::class, 'id', 'left_currency_id')->value('name');
    }
    public function getRCurrencyAttribute()
    {
        return $this->hasOne(Currency::class, 'id', 'right_currency_id')->value('name');
    }

    protected function getStatusNameAttribute()
    {
        $language = session()->get('lang');
        $zn = ['闪兑中', '已通过', '已驳回'];
        $en = ['flash in', 'passed', 'overruled'];
        $value=$this->attributes['status'];
        if ($language != 'en') {
            return $zn[$value];
        } else {
            return $en[$value];
        }
    }


    public function getCreateTimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['create_time']);
    }
    public function getReviewTimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['create_time']);
    }

}
