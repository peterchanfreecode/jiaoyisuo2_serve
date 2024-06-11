<?php

namespace App\Http\Controllers\Api;

use App\Models\SystemWallet;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\FlashAgainst;
use App\Models\Users;
use App\Models\UsersWallet;
use App\Models\Currency;
use App\Models\AccountLog;
use App\Models\UsersWalletOut;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    //我的资产
    public function walletList(Request $request)
    {
        $currency_name = $request->input('currency_name', '');
        $user_id = Users::getUserId();
        if (empty($user_id)) {
            return $this->error('参数错误');
        }
        $change_wallet['CNY'] = '';
        $change_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currencyCoin', function ($query) use ($currency_name) {
                empty($currency_name) || $query->where('name', 'like', '%' . $currency_name . '%');
            })->get(['id', 'currency', 'change_balance', 'lock_change_balance'])
            ->toArray();
        $change_wallet['totle'] = 0;
        $change_wallet['usdt_totle'] = 0;
        foreach ($change_wallet['balance'] as $k => $v) {
            $num = $v['change_balance'] /*+ $v['lock_change_balance']*/
            ;
            $change_wallet['usdt_totle'] += $num * $v['usdt_price'];
        }
        $ExRate = Setting::getValueByKey('USDTRate', 6.5);
        //读取是否开启充提币
        $is_open_CTbi = Setting::where("key", "=", "is_open_CTbi")->first()->value;
        return $this->success([
            'change_wallet' => $change_wallet,
            'ExRate' => $ExRate,
            "is_open_CTbi" => $is_open_CTbi
        ]);
    }

    //充币地址

    //充币地址
    public function getWalletAddressIn()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        if (empty($currency_id)) {
            return $this->error('参数错误');
        }
        $arr = [];
        $currencyInfo = SystemWallet::where("currency_id", $currency_id)->get()->toArray();
        if (!$currencyInfo) {
            return $this->error('参数错误');
        }
        $user_i = intval(substr($user_id, -1));
        if ($currency_id == 3) {
            $res = [];
            $res2 = [];
            foreach ($currencyInfo as $v) {
                if (strtolower($v["type"]) == "erc20") {
                    $res[] = $v;
                } else {
                    $res2[] = $v;
                }
            }
            if ($res) {
                $arr[] = $res[$user_i] ?? $res[0];
            }
            if ($res2) {
                $arr[] = $res2[$user_i] ?? $res2[0];
            }

        } else {
            if ($currencyInfo) {
                $arr[] = $currencyInfo[$user_i] ?? $currencyInfo[0];
            }

        }
        return $this->success($arr);
    }

    //充币地址
    public function getWalletAddress()
    {
        $currencyInfo = SystemWallet::get();
        if (!$currencyInfo) {
            return $this->error('参数错误');
        }
        $addr = [];
        $r = [];
        foreach ($currencyInfo as $v) {
            if (!in_array($v["currency_id"], $r)) {
                $addr[] = [
                    "currency_id" => $v["currency_id"],
                    "currency_name" => $v["currency_name"],
                    "icon" => $v["icon"],
                ];
                $r[] = $v["currency_id"];
            }

        }
        return $this->success($addr);
    }

    public function chargeReq()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        $number = Input::get("amount", '');
        $account = Input::get("account", 0);
        $charge_url = Input::get("charge_url", '');
        if (!$currency_id) {
            return $this->error('请选择充值币种');
        }
        if (!$number) {
            return $this->error('请输入充值数量');
        }
        if ($number < 0) {
            return $this->error('输入的金额不能为负数');
        }
        if (!$charge_url) {
            return $this->error('请上传充值截图');
        }
        if ($charge_url == "undefined") {
            return $this->error('请上传充值截图');
        }
        $currency = Db::table('currency')->where('id', $currency_id)->first();
        if (!$currency) {
            return $this->error('不支持此币种充值');
        }
        $rechargeRate = Setting::where('key', 'recharge_rate')->value('value');
        $data = [
            'uid' => $user_id,
            'currency_id' => $currency_id,
            'amount' => bcmul($number, bcsub(1, $rechargeRate, 8), 8),
            'user_account' => $account,
            'charge_url' => $charge_url,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        Db::table('charge_req')->insert($data);

        return $this->success('成功');
    }

    public function getCurrencyInfo()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        if (empty($currency_id)) return $this->error('参数错误:(');
        $currencyInfo = Currency::find($currency_id);
        if (empty($currencyInfo)) return $this->error('币种不存在');
        $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first();
        $data = [
            'rate' => $currencyInfo->rate,
            'min_number' => $currencyInfo->min_number,
            'name' => $currencyInfo->name,
            'change_balance' => $wallet->change_balance,
        ];
        return $this->success($data);
    }

    //提交提币信息。数量。
    public function postWalletOut()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        $number = Input::get("number", '');
        $rate = Input::get("rate", '');
        $address = Input::get("address", '');
        $password = Input::get('pay_password');
        if (!$currency_id || !$number || !$address) {
            return $this->error('参数错误');
        }
        if ($number < 0) {
            return $this->error('输入的金额不能为负数');
        }
        $user = Users::getById(Users::getUserId());
        if ($user->pay_password != Users::makePassword($password)) {
            return $this->error('密码错误');
        }
        if ($user->score < 100) {
            return $this->error('信用分不足请联系客服');
        }
        if (isset($user->is_withdraw)) {
            if ($user->is_withdraw == -1) {
                return $this->error('交易量不足,请先完成交易');
            }
        }
        $currencyInfo = Currency::find($currency_id);
        if ($number < $currencyInfo->min_number) {
            return $this->error('数量不能少于最小值');
        }
        try {
            DB::beginTransaction();
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->lockForUpdate()->first();

            if ($number > $wallet->change_balance) {
                DB::rollBack();
                return $this->error('余额不足');
            }
            $walletOut = new UsersWalletOut();
            $walletOut->user_id = $user_id;
            $walletOut->currency = $currency_id;
            $walletOut->number = $number;
            $walletOut->address = $address;
            $walletOut->rate = bcmul($number, $currencyInfo['rate'], 8);
            $walletOut->real_number = bcsub($number, $walletOut->rate, 8);
            $walletOut->create_time = time();
            $walletOut->status = 1; //1提交提币2已经提币3失败
            $walletOut->save();

            $result = change_wallet_balance($wallet, 2, -$number, AccountLog::WALLETOUT, '申请提币扣除余额');
            if ($result !== true) {
                throw new \Exception($result);
            }
            $result = change_wallet_balance($wallet, 2, $number, AccountLog::WALLETOUT, '申请提币冻结余额', true);
            if ($result !== true) {
                throw new \Exception($result);
            }
            DB::commit();
            return $this->success('成功');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

    //余额页面详情
    public function getWalletDetail()
    {
        // return $this->error('参数错误');
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        $type = Input::get("type", '');
        if (empty($user_id) || empty($currency_id)) {
            return $this->error('参数错误');
        }
        $ExRate = Setting::getValueByKey('USDTRate', 6.5);
        $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first(['id', 'currency', 'change_balance', 'lock_change_balance', 'address']);
        if (empty($wallet)) return $this->error("钱包未找到");

        $wallet->ExRate = $ExRate;
        if (in_array($wallet->currency, [1, 2, 3])) {
            $wallet->is_charge = true;
        } else {
            $wallet->is_charge = false;
        }
        $wallet->coin_trade_fee = Setting::getValueByKey('COIN_TRADE_FEE');
        return $this->success($wallet);
    }

    public function legalLog(Request $request)
    {

        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        $currency = $request->get('currency', 0);
        $type = $request->get('type', 0);
        $user_id = Users::getUserId();
        $list = new AccountLog();
        if (!empty($currency)) {
            $list = $list->where('currency', $currency);
        }
        if (!empty($user_id)) {
            $list = $list->where('user_id', $user_id);
        }
        if (!empty($type)) {
            $list = $list->whereHas('walletLog', function ($query) use ($type) {
                $query->where('balance_type', $type);
            });
        }
        $list = $list->orderBy('id', 'desc')->paginate($limit);

        $is_open_CTbi = Setting::where("key", "=", "is_open_CTbi")->first()->value;
        return $this->success(array(
            "list" => $list->items(), 'count' => $list->total(),
            "limit" => $limit,
            "is_open_CTbi" => $is_open_CTbi
        ));
    }

    //闪兑信息
    public function flashAgainstList(Request $request)
    {
        $user_id = Users::getUserId();
        $left = Currency::where('is_match', 1)->get();
        foreach ($left as $k => $v) {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $v->id)->first();
            if (empty($wallet)) {
                $balance = 0;
            } else {
                $balance = bcadd($wallet->change_balance, 0, 4);
            }
            $v->balance = $balance;
            $v->price = bcadd($v->price, 0, 4);
            $left[$k] = $v;
        }
        $right = Currency::where('is_micro', 1)->get();
        foreach ($right as $k => $v) {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $v->id)->first();
            if (empty($wallet)) {
                $balance = 0;
            } else {
                $balance = bcadd($wallet->change_balance, 0, 4);
            }
            $v->balance = $balance;
            $v->price = bcadd($v->price, 0, 4);
            $right[$k] = $v;
        }
        return $this->success(['left' => $left, 'right' => $right]);
    }

    public function flashAgainst(Request $request)
    {
        try {
            $l_currency_id = $request->get('l_currency_id', "");
            $r_currency_id = $request->get('r_currency_id', "");
            $num = $request->get('num', 0);

            $user_id = Users::getUserId();
            if ($num <= 0) return $this->error('数量不能小于等于0');
            $p = $request->get('price', 0);
            if ($p <= 0) return $this->error('价格不能小于等于0');

            if (empty($l_currency_id) || empty($r_currency_id)) return $this->error('参数错误哦');

            $left = Currency::where('id', $l_currency_id)->first();
            $right = Currency::where('id', $r_currency_id)->first();
            if (empty($left) || empty($right)) return $this->error('币种不存在');
            $rate = Setting::getValueByKey('exchange_rate', 0);
            $fee = 0;
            if ($rate) {
                $absolute_quantity = bc_div(bc_mul(bc_mul($left->price, $num), (1 - $rate)), $right->price);
                $fee = bc_mul(bc_mul($left->price, $num), $rate);
            } else {
                $absolute_quantity = bc_div(bc_mul($left->price, $num), $right->price);
            }

            DB::beginTransaction();

            $l_wallet = UsersWallet::where('currency', $l_currency_id)->where('user_id', $user_id)->lockForUpdate()->first();
            if (empty($l_wallet)) {

                throw new \Exception('钱包不存在');
            }

            if ($l_wallet->change_balance < $num) {

                throw new \Exception('金额不足');
            }
            $r_wallet = UsersWallet::where('currency', $r_currency_id)->where('user_id', $user_id)->lockForUpdate()->first();
            if (empty($r_wallet)) {

                throw new \Exception('钱包不存在');
            }

            $flash_against = new FlashAgainst();
            $flash_against->user_id = $user_id;
            $flash_against->price = $p;
            $flash_against->fee = $fee;
            $flash_against->market_price = $left->price;
            $flash_against->num = $num;
            $flash_against->status = 1;;
            $flash_against->review_time = time();
            $flash_against->left_currency_id = $l_currency_id;
            $flash_against->right_currency_id = $r_currency_id;
            $flash_against->create_time = time();
            $flash_against->absolute_quantity = $absolute_quantity; //实际数量
            $flash_against->save();
            change_wallet_balance($l_wallet, 2, -$num, AccountLog::DEBIT_BALANCE_MINUS, '币币兑换扣除余额');
            change_wallet_balance($r_wallet, 2, $absolute_quantity, AccountLog::DEBIT_BALANCE_MINUS, '币币兑换通过,增加余额');
            DB::commit();
            return $this->success('资产兑换成功!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function myFlashAgainstList(Request $request)
    {
        $limit = $request->get('limit', 10);
        $user_id = Users::getUserId();
        $list = FlashAgainst::orderBy('id', 'desc')->where('user_id', $user_id)->paginate($limit);
        return $this->success($list);
    }
}
