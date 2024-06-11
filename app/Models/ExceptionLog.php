<?php
/**
 * create by vscode
 * @author lion
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ExceptionLog extends Model
{
    protected $table = 'exception_log';
    public $timestamps = false;

    public static function set_log($title, $message)
    {
        $info = self::where("message", $message)->first();
        if ($info) {
            $info->created_at = date("Y-m-d H:i:s", time());
            $info->save();
            return true;
        }
        $model = new self();
        $model->title = $title;
        $model->message = $message;
        $model->created_at = date("Y-m-d H:i:s", time());
        return $model->save();
    }
}
