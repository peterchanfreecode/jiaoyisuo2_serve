<?php

namespace App\Http\Controllers\Admin;

use App\Events\CandyEvent;
use App\Events\RechargeEvent;
use App\Models\AccountLog;
use App\Models\NewCurrency;
use App\Models\NewCurrencyOrder;
use App\Models\UsersWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
class NewCurrencyController extends Controller
{
    public function index()
    {
        return view('admin.new_currency.index');
    }

    public function add(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            $result = new NewCurrency();
        } else {
            $result = NewCurrency::find($id);
        }
        $currency = Currency::select(["id", "name", "price"])->get()->toArray();
        return view('admin.new_currency.add')->with('result', $result)->with('currency', $currency);
    }

    public function get_price(Request $request)
    {
        $currency_id = $request->get('currency_id', 0);
        if (!$currency_id) {
            return $this->error('请选择币种');
        }
        $currency = Currency::select(["price"])->where("id", $currency_id)->first();
        return $this->success($currency);
    }

    public function postAdd(Request $request)
    {
        $id = $request->get('id', 0);
        $data = $request->post();
        unset($data['_token']);
        try {
            if (empty($id)) {
                $model = new NewCurrency();
                $model->forceFill($data)->save();
            } else {
                NewCurrency::where('id', $id)->update($data);
                NewCurrencyOrder::whereRaw('nc_id = ? and is_thaw = 0', [$id])->update([
                    'lock_end' => $data['lock_end']
                ]);
            }
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $result = new NewCurrency();
        $result = $result->leftJoin('currency', 'currency.id', '=', 'new_currency.currency')
            ->select('new_currency.*', 'currency.name as currency_name')->orderBy('id', 'desc')
            ->paginate($limit);
        return $this->layuiData($result);
    }

    public function order(Request $request)
    {
        $id = $request->get('id', 0);

        return view('admin.new_currency.order')->with('id', $id);
    }

    public function getOrder(Request $request)
    {
        $id = $request->get('id', 0);
        $limit = $request->get('limit', 10);
        $account_number = $request->get('account_number');
        $status = $request->get('status', -1);
        $uid = $request->get('uid', "");
        $result = new NewCurrencyOrder();
        $where = ['new_currency_order.nc_id' => $id];
        if (!empty($account_number)) {
            $where['users.account_number'] = $account_number;
        }
        if ($uid) {
            $where['new_currency_order.uid'] = $uid;
        }
        if ($status != '-1') {
            $where['new_currency_order.status'] = $status;
        }
        $result = $result->leftJoin('users', 'users.id', '=', 'new_currency_order.uid')
            ->leftJoin('new_currency', 'new_currency.id', '=', 'new_currency_order.nc_id')
            ->where($where)
            ->select('new_currency_order.*', 'users.account_number', 'new_currency.title')
            ->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }

    // 拒绝
    public function refuse(Request $request)
    {
        $id = $request->get('id', 0);
        $order = NewCurrencyOrder::where('id', $id)->first();
        if (empty($order) || $order['status'] != 0) {
            return $this->error('只有待审核的订单才能拒绝');
        }
        try {
            DB::beginTransaction();
            $user_wallet = UsersWallet::where('user_id', $order['uid'])
                ->where('currency', $order['pay_currency_id'])->lockForUpdate()->first();
            change_wallet_balance($user_wallet, 2, $order['coin_amount'], AccountLog::IEO_REFUSE, 'IEO申购被拒绝，返还');
            NewCurrencyOrder::where('id', $id)->update(['status' => 1]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
        return $this->success('操作成功');
    }

    // 通过view
    public function passView(Request $request)
    {
        $id = $request->get('id', 0);
        $result = NewCurrencyOrder::find($id);
        $currency = NewCurrency::where('id', $result['nc_id'])->first();
        return view('admin.new_currency.pass')->with('result', $result)->with('title', $currency['title']);
    }

    // 通过
    public function pass(Request $request)
    {
        $id = $request->get('id', 0);
        $desc = $request->get('desc', '');
        $rate = intval($request->get('rate', 0));
        $order = NewCurrencyOrder::where('id', $id)->first();
        if (empty($order) || $order['status'] != 0) {
            return $this->error('只有待审核的订单才能拒绝');
        }
        if ($rate <= 0) {
            return $this->error('中签率必须大于0');
        }
        $currency = NewCurrency::where('id', $order['nc_id'])->first();
        try {
            DB::beginTransaction();
            $getCoinAmount = bcmul($order['coin_amount'], $rate / 100, 8); // usdt 中标数量
            $getApplyAmount = bcmul($order['apply_amount'], $rate / 100, 8); // 申购币种 中标数量

            $user_wallet = UsersWallet::where('user_id', $order['uid'])
                ->where('currency', $order['currency'])->first();
            if (empty($user_wallet)) {
                $wallet = [
                    'user_id' => $order['uid'],
                    'currency' => $order['currency']
                ];
                $model = new UsersWallet();
                $model->forceFill($wallet)->save();
            }

            $user_wallet = UsersWallet::where('user_id', $order['uid'])
                ->where('currency', $order['currency'])->lockForUpdate()->first();

            $isLock = true;
            if (Carbon::now()->toDateString() > $currency['lock_end']) {
                $isLock = false;
            }
            change_wallet_balance($user_wallet, 2, $getApplyAmount, AccountLog::IEO_PASS, '新币申购成功', $isLock);

            if ($rate > 0 && $rate < 100) {
                $user_wallet2 = UsersWallet::where('user_id', $order['uid'])
                    ->where('currency', $order['pay_currency_id'])->lockForUpdate()->first();
                $amount = $order['coin_amount'] - $getCoinAmount;
                change_wallet_balance($user_wallet2, 2, $amount, AccountLog::IEO_REFUSE, 'IEO申购未中签，返还余额');
            }
            NewCurrencyOrder::where('id', $id)->update([
                'get_coin_amount' => $getCoinAmount,
                'get_apply_amount' => $getApplyAmount,
                'rate' => $rate,
                'desc' => $desc,
                'status' => 2,
                'is_thaw' => $isLock ? 0 : 1,
                'lock_end' => $currency['lock_end']
            ]);
            event(new CandyEvent( $order['uid'],$getCoinAmount));
            event(new RechargeEvent( $order['uid'],2));//新币购买事件
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
        return $this->success('操作成功');
    }

    public function del(Request $request)
    {
        $id = $request->get('id', 0);
        $result = NewCurrency::find($id);
        if (empty($result)) {
            return $this->error('参数错误');
        }
        try {
            $result->delete();
            return $this->success('删除成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function status($startAt, $endAt)
    {
        $now = Carbon::now()->toDateTimeString();
        if ($now < $startAt) {
            $status = 0; // 未开始
        } else if ($now > $endAt) {
            $status = 2; // 已结束
        } else {
            $status = 1; // 进行中
        }
        return $status;
    }
}
