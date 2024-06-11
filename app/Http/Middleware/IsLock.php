<?php

namespace App\Http\Middleware;

use App\Models\Users;
use Closure;
use App\Service\ResponseService;
class IsLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_id = Users::getUserId();
        //判断是否锁定账户 3.12 by t
        $users=Users::where('id',$user_id)->first();
        if ($users->status == 1){//1锁定   0不锁定
            return ResponseService::success("您处于锁定状态", 'error');
        }

        return $next($request);
    }
}
