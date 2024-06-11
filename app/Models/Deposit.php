<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = 'deposit';

    public function getRateAttribute($rate)
    {
        return sprintf("%.5f", $rate);
    }

    public function getSaveMinAttribute($saveMin)
    {
        return sprintf("%.5f", $saveMin);
    }
}
