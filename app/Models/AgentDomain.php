<?php

/**
 * Created by PhpStorm.
 * User: zef
 * Date: 2018/11/23
 * Time: 10:23
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AgentDomain extends Model
{
    protected $table = 'agent_domain';
    public $timestamps = false;
    protected $appends = [
        'level',
        'username',
        'user_id'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id', 'id');

    }

    public function getLevelAttribute()
    {
        return $this->agent()->value('level');
    }

    public function getUsernameAttribute()
    {
        return $this->agent()->value('username');
    }

    public function getUserIdAttribute()
    {
        return $this->agent()->value('user_id');
    }
}
