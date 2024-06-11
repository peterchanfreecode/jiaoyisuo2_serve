<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewCurrency extends Model
{
    protected $table = 'new_currency';

    use SoftDeletes;

    public function getStatusAttribute() {
        return $this->status2($this->raise_start, $this->raise_end);
    }

    public function status2($startAt, $endAt)
    {

        $now = Carbon::now()->toDateTimeString();
        $endAt = date("Y-m-d",strtotime($endAt)+86400);
        if ($now < $startAt) {
            $status = 0; // 未开始
        } else if ($now > $endAt) {
            $status = 2; // 已结束
        } else {
            $status = 1; // 进行中
        }
        return $status;
    }
}
