<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RebateFlow extends Model
{
    protected $table = 'rebate_flow';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(Users::class, 'receive_user_id', 'id')->withDefault();
    }
}