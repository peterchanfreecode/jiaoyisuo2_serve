<?php

/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemWallet extends Model
{

    protected $table = 'system_wallet';
    public $timestamps = false;
    protected $guarded = [];
    protected $appends = [
        'currency_name',
         'icon'
    ];
    public function getCurrencyNameAttribute()
    {
        return $this->currency()->value('name');
    }
    public function getIconAttribute()
    {
        return strtolower($this->currency()->value('name'));
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
