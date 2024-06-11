<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountLog;
use App\Models\Currency;
use App\Models\NewCurrency;
use App\Models\NewCurrencyOrder;
use App\Models\Users;
use App\Models\UsersWallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class NewCurrencyController extends Controller
{

    // IEO认购
    public function lists()
    {
        $page = Input::get('page', 1);
        $limit = Input::get('limit', 30);
        $tab = Input::get('tab', 0);
        $time = Carbon::now()->toDateString();
        switch ($tab) {
            case 0:
                $where = 'new_currency.raise_start > ?';
                $binds = [$time];
                break;
            case 1:
                $where = 'new_currency.raise_start <= ? and new_currency.raise_end >= ?';
                $binds = [$time, $time];
                break;
            case 2:
                $where = 'new_currency.raise_end < ?';
                $binds = [$time];
                break;
        }
        $projects = NewCurrency::from('new_currency')
            ->leftJoin('currency', 'currency.id', '=', 'new_currency.currency')
            ->whereRaw($where, $binds)
            ->select('new_currency.id', 'new_currency.title', 'new_currency.amount', 'new_currency.start_at',
                'new_currency.raise_start', 'new_currency.raise_end', 'currency.name as currency_name',
                'currency.id as currency_id', 'new_currency.lock_start', 'new_currency.deleted_at',
                'new_currency.lock_end', 'progress', 'min', 'max')
            ->orderBy('id', 'desc')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get()->toArray();
        $projects = array_map(function ($project) {
            $project['status'] = $this->status($project['raise_start'], $project['raise_end']);
            $project['use_amount'] = bcmul($project['amount'], $project['progress'] / 100, 0);
            return $project;
        }, $projects);
        return $this->success($projects);
    }

    // 详情
    public function detail()
    {
        $id = Input::get('id', 0);
        $detail = NewCurrency::where('id', $id)->select(
            'id', 'title', 'price', 'start_at', 'progress', 'amount', 'currency', 'pay_currency_id',
            'show_start', 'show_end', 'raise_start', 'raise_end', 'lock_start', 'lock_end'
        )->first();
        if (empty($detail)) {
            return $this->error('项目不存在');
        }
        $detail['use_amount'] = bcmul($detail['amount'], $detail['progress'] / 100, 0);
        $detail['currency_name'] = Currency::where('id', $detail['currency'])->value('name');
        $detail['pay_currency_name'] = Currency::where('id', $detail['pay_currency_id'])->value('name');
        return $this->success($detail);
    }

    // 认购
    public function buy()
    {
        $id = intval(Input::get('id', 0));
        $userId = Users::getUserId();
        $coinAmount = intval(Input::get('coin_amount', 0)); // 申购的usdt数量
        $newCurrency = NewCurrency::where('id', $id)->first()->toArray();
        if (empty($newCurrency)) {
            return $this->error('项目不存在');
        }
        $now = Carbon::now()->toDateString();
//        if ($newCurrency['status'] == 0) {
//            return $this->error('未开始');
//        } else if ($newCurrency['status'] == 2) {
//            return $this->error('已结束');
//        }
        if ($now < $newCurrency['raise_start'] || $now > $newCurrency['raise_end']) {
            return $this->error('不在可筹集时间范围内');
        }
        if ($coinAmount < $newCurrency['min']) {
            return $this->error('小于最低认购');
        }
        if ($coinAmount > $newCurrency['max']) {
            return $this->error('大于最高低认购');
        }
        DB::beginTransaction();
        try {
            $useAmount = bcmul($newCurrency['amount'], $newCurrency['progress'] / 100, 0); // 已配售数量
            $num = bcdiv($coinAmount, $newCurrency['price'], 8); // 申请的配售数量
         /*   if ($num > bcsub($newCurrency['amount'], $useAmount, 0)) {*/
            if ($num > $useAmount) {
                return $this->error('配售额度不足');
            }
            $user_wallet = UsersWallet::where('user_id', $userId)
                ->where('currency', $newCurrency['pay_currency_id'])->lockForUpdate()->first();
            if (empty($user_wallet) || $user_wallet['change_balance'] < $coinAmount) {
                return $this->error('余额不足');
            }
            change_wallet_balance($user_wallet, 2, -$coinAmount, AccountLog::IEO_BUY, 'IEO申购');
            $currencyName = Currency::where('id', $newCurrency['currency'])->value('name');
            $payCurrencyName = Currency::where('id', $newCurrency['pay_currency_id'])->value('name');
            // 插入申购订单
            $order = [
                'uid' => $userId,
                'nc_id' => $id,
                'currency' => $newCurrency['currency'],
                'pay_currency_id' => $newCurrency['pay_currency_id'],
                'currency_name' => $currencyName,
                'pay_currency_name' => $payCurrencyName,
                'coin_amount' => $coinAmount, // usdt数量
                'apply_amount' => $num,
                'price' => $newCurrency['price'],
            ];
            $model = new NewCurrencyOrder();
            $model->forceFill($order)->save();
            DB::commit();
            return $this->success('申购成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    // 我的申购
    public function myOrder()
    {
        $page = Input::get('page', 1);
        $limit = Input::get('limit', 30);
        $userId = Users::getUserId();
        $data = NewCurrencyOrder::where('uid', $userId)
            ->leftJoin('new_currency', 'new_currency.id', '=', 'new_currency_order.nc_id')
            ->select('new_currency.title', 'new_currency_order.id', 'new_currency_order.currency', 'new_currency_order.pay_currency_id',
                'new_currency_order.coin_amount', 'new_currency_order.get_coin_amount',
                'new_currency_order.created_at', 'new_currency_order.status')
            ->orderBy('id', 'desc')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get()->map(function ($order) {
                $order['currency_name'] =  Currency::where('id', $order['currency'])->value('name');
                $order['pay_currency_name'] =  Currency::where('id', $order['pay_currency_id'])->value('name');
                return $order;
            })->toArray();
        return $this->success($data);
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
