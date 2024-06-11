<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReal extends Model
{
    protected $table = 'user_real';
    public $timestamps = false;
    protected $hidden = [];
    protected $appends = ['account'];

    public function getCreateTimeAttribute()
    {
        $value = $this->attributes['create_time'];
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function getAccountAttribute()
    {

        $res = $this->belongsTo(Users::class, 'user_id', 'id')->value('phone');
        if (empty($res)) {
            $res = $this->belongsTo(Users::class, 'user_id', 'id')->value('email');
        }
        return $res;

    }

    public function getFrontPicAttribute()
    {
        $value = $this->attributes['front_pic'];

        if (stristr($value, 'http')) {
            return $value;
        } else {
            return config('app.aws_url') . $value;
        }
    }

    public function getReversePicAttribute()
    {
        $value = $this->attributes['reverse_pic'];
        if (stristr($value, 'http')) {
            return $value;
        } else {
            return config('app.aws_url') . $value;
        }
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

    public function getTypeAttribute($type)
    {
        $arr = ['身份证', '护照', '驾驶证'];
        return $arr[$type];
    }
}