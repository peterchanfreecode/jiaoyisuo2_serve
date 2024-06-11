<?php

namespace App\Http\Middleware;

use App\Service\ResponseService;
use Closure;
use Illuminate\Http\Request;
use App\Models\AdminIp;

class CheckIp
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->header("x-real-ip") ?? request()->getClientIp();
        $info = AdminIp::where("ip", $ip)->first();
        if (!$info) {
            return redirect('/error');
        }
        return $next($request);
    }
}