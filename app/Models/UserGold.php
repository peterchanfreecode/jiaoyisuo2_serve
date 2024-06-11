<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGold extends Model
{
    protected $table = 'user_gold';
    public $timestamps = false;
    protected $hidden = [];
    protected $appends = ['account'];

    public function getAccountAttribute()
    {

        $res = $this->belongsTo(Users::class, 'user_id', 'id')->value('phone');
        if (empty($res)) {
            $res = $this->belongsTo(Users::class, 'user_id', 'id')->value('email');
        }
        return $res;

    }
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

}