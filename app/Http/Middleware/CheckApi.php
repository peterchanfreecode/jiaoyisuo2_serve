<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;
use App\Service\ResponseService;
class CheckApi
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $token = Token::getToken();
        $user_id = Token::getUserIdByToken($token);
        if (empty($user_id)) {
            return ResponseService::success("请登录", '999');
        }
        return $next($request)->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
