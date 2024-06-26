<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Users;
use App\Models\LeverTransaction;
use App\Service\ResponseService;

class HoldCheck
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
        $exist_close_trade = LeverTransaction::where('user_id', $user_id)->whereNotIn('status', [LeverTransaction::CLOSED, LeverTransaction::CANCEL])->count();
        if ($exist_close_trade > 0) {
            return ResponseService::success("操作失败:您有未平仓的交易,操作禁止", 'error');

        }
        return $next($request);
    }
}
