<?php
namespace App\Service;

use Illuminate\Support\Facades\DB;

class UserService
{
    public static function getPan($userid)
    {
        return DB::table('users')->where('id', $userid)->value('pan_type');
    }
}