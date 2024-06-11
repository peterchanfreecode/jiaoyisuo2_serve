<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeverMultiple extends Model
{
    protected $table = 'lever_multiple';
    public $timestamps = false;
    protected $appends = [
        'currency_name',

    ];

    public function getCurrencyNameAttribute()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id')->value('name');
    }

    public function getQuotesAttribute()
    {
        return unserialize($this->attributes['quotes']);
    }
}
