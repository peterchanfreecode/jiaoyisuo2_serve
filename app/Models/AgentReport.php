<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class AgentReport extends Model
{
    protected $table = 'agent_report';
    public $timestamps = false;
    protected $appends = [
        'agent_name',
        'user_id'
    ];
    public function getAgentNameAttribute()
    {
        return $this->hasOne(Agent::class, 'id', 'agent_id')->value('username');
    }
    public function getUserIdAttribute()
    {
        return $this->hasOne(Agent::class, 'id', 'agent_id')->value('user_id');
    }
    public function Agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id', 'id');
    }
}
