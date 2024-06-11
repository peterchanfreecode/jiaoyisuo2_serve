<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Users;
use App\Service\ResponseService;
class ValidateUserLocked
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
        $user = Users::find($user_id);
        if ($user) {
            if ($user->status == 1 && $user->lock_time > time()) {
                return ResponseService::success("您处于锁定状态", 'error');
            }
        } else {
            return ResponseService::success("请登录", '999');
        }
        return $next($request);
    }
}
