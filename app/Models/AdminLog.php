<?php
/**
 * create by vscode
 * @author lion
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $table = 'admin_log';
    public $timestamps = false;

    public static function set_log($admin_id, $type, $ip)
    {
        $model = new self();
        $model->admin_id = $admin_id;
        $model->type = $type;
        $model->ip = $ip;
        return $model->save();
    }
}
