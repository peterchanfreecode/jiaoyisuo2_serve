<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountLog;
use App\Models\Users;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends Controller
{

    public function chargeMentionMoney(Request $request)
    {
        $limit = $request->get('limit', 5);
        $user_id = Users::getUserId();
        $arr = [AccountLog::CHANGEREQ, AccountLog::WALLETOUT];
        $currency = $request->get('currency', -1);
        $list = AccountLog::where(function ($query) use ($currency) {
            $currency != -1 && $query->where('currency', $currency);
        })->whereIn('type', $arr)
            ->where('user_id', $user_id)
            ->orderBy('id', 'desc')
            ->paginate($limit);
        return $this->success($list);
    }

}
