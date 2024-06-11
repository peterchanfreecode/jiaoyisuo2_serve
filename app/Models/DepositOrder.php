<?php

/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DepositOrder extends Model
{
    protected $table = 'deposit_order';
    protected $guarded = [];
    protected $appends = [
        'currency_name',
        'user_name',
        "level_name",
        "is_end",
    ];

    public function getCurrencyNameAttribute()
    {
        return $this->currency()->value('name');
    }

    public function getLevelNameAttribute()
    {
        return $this->deposit()->value('level_zh');
    }

    public function getIsEndAttribute()
    {
        $now = Carbon::now()->subDay()->toDateString();
        $end_time = $this->attributes['end_at'];
        if ($now >= $end_time && $this->attributes['status'] == 1) {
            return 1;
        } else {
            return 0;
        }

    }

    public function getUserNameAttribute()
    {
        return $this->user()->value('account_number');
    }

    public static function saveOrder($order)
    {
        $model = new DepositOrder();
        return $model->forceFill($order)->save();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

    // 今日预估收益
    public static function todayMay($userId)
    {
        $now = Carbon::now()->toDateString();
        $orders = DepositOrder::whereRaw('user_id = ? and status = 1 and start_at <= ? and end_at >= ?',
            [$userId, $now, $now])->get()->toArray();
        if (empty($orders)) {
            return 0;
        }
        $total = 0;
        foreach ($orders as $order) {
            $total = bcadd($total, bcmul($order['amount'], ($order['day_rate'] / 100), 8), 8);
        }
        return $total;
    }
}
