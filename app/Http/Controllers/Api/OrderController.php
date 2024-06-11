<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\ChargeReq;
use App\Models\UsersWalletOut;
class OrderController extends Controller
{
    public function charge_list(Request $request)
    {
        try {
            $user_id = Users::getUserId();
            $limit = $request->input('limit', 10);
            $lists = ChargeReq::where('uid', $user_id)
                ->orderBy('id', 'desc')
                ->paginate($limit);
            return $this->success($lists);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
    public function wallet_out_list(Request $request)
    {
        try {
            $user_id = Users::getUserId();
            $limit = $request->input('limit', 10);
            $lists = UsersWalletOut::where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->paginate($limit);
            return $this->success($lists);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

}
