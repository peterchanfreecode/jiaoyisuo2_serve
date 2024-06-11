<?php

namespace App\Http\Middleware;

use App\Models\Users;
use Closure;
use App\Models\UserReal;
use App\Service\ResponseService;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_id = Users::getUserId();
        $user_real = UserReal::where('user_id', $user_id)->first();
        if (empty($user_real)) {
            $message = '请实名认证';
            $message = str_replace('massage.', '', __("massage.$message"));
            return ResponseService::success($message, '998');
        }
        if ($user_real->status != 2) {
            return ResponseService::success("您的实名认证还未通过!", 'error');
        }
        return $next($request);
    }
}
