<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccountLog;
use App\Models\Currency;
use Symfony\Component\HttpFoundation\Request;

class AccountLogController extends Controller
{

    public function index()
    {
        //获取type类型
        $type = array(
            AccountLog::CHANGEREQ => '充值',
            AccountLog::ADMIN_CHANGE_BALANCE => '人工充值',
            AccountLog::WALLETOUT => '提现',
            AccountLog::TRANSACTIONIN => '币币交易',
            AccountLog::TRANSACTION_FEE => '币币交易手续费',
            AccountLog::MICRO_TRADE => '秒合约',
            AccountLog::MICRO_TRADE_FREE => '秒合约手续费',
            AccountLog::LH_LOAN => '质押',
            AccountLog::IEO_BUY => '申购',
            AccountLog::IEO_REFUSE => '申购拒绝',
            AccountLog::IEO_PASS => '申购通过',
            AccountLog::IEO_UN_THAW => '申购解冻',
            AccountLog::DEBIT_BALANCE_MINUS => '币币兑换',
        );
        $currency_type = Currency::all();
        return view("admin.account.index", [
            'types' => $type,
            'currency_type' => $currency_type
        ]);
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        $start_time = strtotime($request->get('start_time', 0));
        $end_time = strtotime($request->get('end_time', 0));
        $currency = $request->get('currency_type', 0);
        $user_id = $request->get('user_id', 0);
        $type = $request->get('type', 0);
        $sign = $request->get('sign', 0);//正负号，0所有，1，正，-1，负号

        $list = new AccountLog();
        if (!empty($currency)) {
            $list = $list->where('currency', $currency);
        }
        if (!empty($type)) {
            $list = $list->where('type', $type);
        }
        if (!empty($start_time)) {
            $list = $list->where('created_time', '>=', $start_time);
        }
        if (!empty($end_time)) {
            $list = $list->where('created_time', '<=', $end_time);
        }
        if (!empty($user_id)) {
            $list = $list->where('user_id',  $user_id);
        }
        if (!empty($sign)) {
            if ($sign > 0) {
                $list = $list->where('value', '>', 0);
            } else {
                $list = $list->where('value', '<', 0);
            }

        }

        if (!empty($account)) {
            $list = $list->whereHas('user', function ($query) use ($account) {
                $query->where("account_number", 'like', '%' . $account . '%');
            });
        }

        if (!empty($type)) {
            $sum = $list->sum('value');
        } else {
            $sum = '选择日志类型进行统计';
        }

        $list = $list->orderBy('id', 'desc')->paginate($limit);
        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total(), 'sum' => $sum]);
    }

    public function view(Request $request)
    {
        $id = $request->get('id', null);
        $results = new AccountLog();
        $results = $results->where('id', $id)->first();
        if (empty($results)) {
            return $this->error('无此记录');
        }
        return view('admin.account.viewDetail', ['results' => $results]);
    }


    public function indexprofits()
    {
        $scene_list = AccountLog::where("type", "=", AccountLog::PROFIT_LOSS_RELEASE)->orderBy("created_time", "desc")->get()->toArray();
//        var_dump($scene_list);die;
        return view('admin.profits.index')->with('scene_list', $scene_list);
    }

    public function listsprofits(Request $request)
    {
        $limit = $request->input('limit', 10);
        $prize_pool = AccountLog::whereHas('user', function ($query) use ($request) {
            $account_number = $request->input('account_number');
            if ($account_number) {
                $query->where('account_number', $account_number);
            }
        })->where(function ($query) use ($request) {
//            $scene = $request->input('scene', -1);
            $start_time = strtotime($request->input('start_time', null));
            $end_time = strtotime($request->input('end_time', null));
//            $scene != -1 && $query->where('scene', $scene);
            $start_time && $query->where('created_time', '>=', $start_time);
            $end_time && $query->where('created_time', '<=', $end_time);
        })->where("type", AccountLog::PROFIT_LOSS_RELEASE)->orderBy('id', 'desc')->paginate($limit);

        return $this->layuiData($prize_pool);
    }

    public function countprofits(Request $request)
    {
        $count_data = AccountLog::selectRaw('1 as user_count')
            ->selectRaw('sum(`value`) as value')
            ->whereHas('user', function ($query) use ($request) {
                $account_number = $request->input('account_number');
                if ($account_number) {
                    $query->where('account_number', $account_number)
                        ->orWhere('phone', $account_number)
                        ->orWhere('email', $account_number);
                }
            })->where(function ($query) use ($request) {
                //$scene = $request->input('scene', -1);
                $start_time = strtotime($request->input('start_time', null));
                $end_time = strtotime($request->input('end_time', null));
                //$scene != -1 && $query->where('scene', $scene);
                $start_time && $query->where('created_time', '>=', $start_time);
                $end_time && $query->where('created_time', '<=', $end_time);
            })->where("type", AccountLog::PROFIT_LOSS_RELEASE)->groupBy('user_id')->get();
        $user_count = $count_data->pluck('user_count')->sum();
        $reward_total = 0;
        $count_data->pluck('value')->each(function ($item, $key) use (&$reward_total) {
            $reward_total = bc_add($reward_total, $item);
        });
        return response()->json([
            'user_count' => $user_count,
            'reward_total' => $reward_total,
        ]);
    }
}
