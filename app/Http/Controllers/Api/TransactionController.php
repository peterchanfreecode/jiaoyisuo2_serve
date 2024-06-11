<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Models\UserChat;
use App\Models\AccountLog;
use App\Models\Transaction;
use App\Models\TransactionComplete;
use App\Models\TransactionIn;
use App\Models\TransactionOut;
use App\Models\Users;
use App\Models\Currency;
use App\Models\UsersWallet;
use Illuminate\Support\Facades\Redis;
use App\Models\NewCurrency;
class TransactionController extends Controller
{
    //正在买入记录
    public function TransactionInList()
    {
        $user_id = Users::getUserId();
        if (empty($user_id)) return $this->error('参数错误');
        $limit = Input::get('limit', 10);
        $page = Input::get('page', 1);
        $transactionIn = TransactionIn::where('user_id', $user_id)->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
        if (empty($transactionIn)) return $this->error('您还没有交易记录');
        return $this->success(array(
            "list" => $transactionIn->items(), 'count' => $transactionIn->total(),
            "page" => $page, "limit" => $limit
        ));
    }

    //正在卖出记录
    public function TransactionOutList()
    {
        $user_id = Users::getUserId();
        if (empty($user_id)) {
            return $this->error('参数错误');
        }
        $limit = Input::get('limit', 10);
        $page = Input::get('page', 1);
        $transactionOut = TransactionOut::where('user_id', $user_id)->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
        if (empty($transactionOut)) {
            return $this->error('您还没有交易记录');
        }
        return $this->success(array(
            "list" => $transactionOut->items(), 'count' => $transactionOut->total(),
            "page" => $page, "limit" => $limit
        ));
    }

    //交易完成记录
    public function TransactionCompleteList()
    {
        $user_id = Users::getUserId();
        $limit = Input::get('limit', 10);
        $page = Input::get('page', 1);
        if (empty($user_id)) {
            return $this->error('参数错误');
        }
        $TransactionComplete = TransactionComplete::where(function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->orwhere('from_user_id', $user_id);
        })
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
        if (empty($TransactionComplete)) {
            return $this->error('您还没有交易记录');
        }
        foreach ($TransactionComplete->items() as $key => &$value) {
            if ($value['type'] == 2) {
                //触发者是买方
                if ($value['user_id'] == $user_id) {
                    $value['type'] = 'in';
                } else {
                    $value['type'] = 'out';
                }
            } elseif ($value['type'] == 1) {
                //触发者是卖方
                if ($value['user_id'] == $user_id) {
                    $value['type'] = 'out';
                } else {
                    $value['type'] = 'in';
                }
            }
        }
        return $this->success(array(
            "list" => $TransactionComplete->items(), 'count' => $TransactionComplete->total(),
            "page" => $page, "limit" => $limit
        ));
    }
    public function out()
    {
        $user_id = Users::getUserId();
        $price = Input::get("price");
        $num = Input::get("num");
        $legal_id = Input::get("legal_id");
        $currency_id = Input::get("currency_id");
        $mode = Input::get("mode");
        if (empty($user_id) || empty($price) || empty($num) || empty($legal_id) || empty($currency_id)) {
            return $this->error("参数错误");
        }
        //验证新币锁仓时间
        $new_info = NewCurrency::where("currency",$currency_id)->first();
        if($new_info){
            $lock_end = strtotime($new_info->lock_end);
            if(time()<$lock_end){
                return $this->error("币种锁仓中,暂不能交易");
            }
        }
        $user = Users::find($user_id);
        $legal = Currency::where("is_display", 1)
            ->where("id", $legal_id)
            ->where("is_legal", 1)
            ->first();
        $currency = Currency::where("is_display", 1)
            ->where("id", $currency_id)
            ->first();


        if (empty($user) || empty($legal) || empty($currency)) {

            return $this->error("数据未找到");
        }
        try {
            DB::beginTransaction();
            $user_currency = UsersWallet::where("user_id", $user_id)
                ->where("currency", $currency_id)
                ->lockForUpdate()
                ->first();
            if (empty($user_currency)) {
                throw new \Exception("请先添加钱包");
            }
            if (bc_comp($price, 0) <= 0 || bc_comp($num, 0) <= 0) {
                throw new \Exception("价格和数量必须大于0");
            }
            if (bc_comp($user_currency->change_balance, $num) < 0) {
                throw new \Exception("您的币不足");
            }
            if (bc_comp($user_currency->lock_change_balance, 0) < 0) {
                throw new \Exception("您的冻结资金异常，禁止挂卖");
            }
            $symbol = strtolower($currency->name . $legal->name);
            $buyPrice = 0;
            if ($symbol) {
                $key = "market.{$symbol}.kline.1min";
                $t = json_decode(Redis::get($key), true);
                $buyPrice = $t['tick']["close"] ?? 0;
                if (!$buyPrice) {
                    throw new \Exception('行情错误');
                }
            }

            if ($mode == 1) {
                $price = $buyPrice;
                TransactionOut::transaction($buyPrice, $num, $user, $user_currency, $legal_id, $currency_id);
            } else if ($mode == 2) {
                if (bc_comp($buyPrice, $price, 8) >= 0) {
                    TransactionOut::transaction($price, $num, $user, $user_currency, $legal_id, $currency_id);
                }
            }
            DB::commit();
            return $this->success("成功");
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

    public function in()
    {
        $user_id = Users::getUserId();

        $price = Input::get("price");
        $num = Input::get("num");
        $legal_id = Input::get("legal_id");
        $currency_id = Input::get("currency_id");
        $mode = intval(Input::get("mode"));
        if (empty($user_id) || empty($price) || empty($num) || empty($legal_id) || empty($currency_id)) {
            return $this->error("参数错误");
        }
        //验证新币锁仓时间
        $new_info = NewCurrency::where("currency",$currency_id)->first();
        if($new_info){
            $lock_end = strtotime($new_info->lock_end);
            if(time()<$lock_end){
                return $this->error("币种锁仓中,暂不能交易");
            }
        }
        $legal = Currency::where("is_display", 1)
            ->where("id", $legal_id)
            ->where("is_legal", 1)
            ->first();
        $currency = Currency::where("is_display", 1)
            ->where("id", $currency_id)
            ->first();

        $user = Users::find($user_id);
        if (empty($user) || empty($legal) || empty($currency)) {
            return $this->error("数据未找到");
        }
        if (bc_comp($price, 0) <= 0 || bc_comp($num, 0) <= 0) {
            return $this->error("价格和数量必须大于0");
        }

        try {
            DB::beginTransaction();
            //买方法币钱包
            $user_legal = UsersWallet::where("user_id", $user_id)
                ->where("currency", $legal_id)
                ->lockForUpdate()
                ->first();
            $all_balance = bc_mul($price, $num, 5);
            if (bc_comp($user_legal->change_balance, $all_balance) < 0) {
                throw new \Exception('余额不足');
            }
            $symbol = strtolower($currency->name . $legal->name);
            $sellPrice = 0;
            if ($symbol) {
                $key = "market.{$symbol}.kline.1min";
                $t = json_decode(Redis::get($key), true);
                $sellPrice = $t['tick']["close"] ?? 0;
                if (!$sellPrice) {
                    throw new \Exception('行情错误');
                }

            }
            if ($mode === 1) {
                $price = $sellPrice;
                TransactionIn::transaction($price, $num, $user, $legal_id, $currency_id);
            } else if ($mode === 2) {
                if (bc_comp($price, $sellPrice, 8) >= 0) {
                    TransactionIn::transaction($price, $num, $user, $legal_id, $currency_id);
                }
            }
            DB::commit();
            return $this->success("成功");
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->error($ex->getMessage());
        }
    }

    public function deal()
    {
        $user_id = Users::getUserId();

        $legal_id = Input::get("legal_id");
        $currency_id = Input::get("currency_id");

        if (empty($legal_id) || empty($currency_id)) {
            return $this->error("参数错误");
        }
        $in = TransactionIn::with(['legalcoin', 'currencycoin'])
            ->where("number", ">", 0)
            ->where("currency", $currency_id)
            ->where("legal", $legal_id)
            ->groupBy('currency', 'legal', 'price')
            ->orderBy('price', 'desc')
            ->select([
                'currency',
                'legal',
                'price',
            ])->selectRaw('sum(`number`) as `number`')
            ->limit(5)
            ->get()
            ->toArray();
        $out = TransactionOut::with(['legalcoin', 'currencycoin'])
            ->where("number", ">", 0)
            ->where("currency", $currency_id)
            ->where("legal", $legal_id)
            ->groupBy('currency', 'legal', 'price')
            ->orderBy('price', 'asc')
            ->select([
                'currency',
                'legal',
                'price',
            ])->selectRaw('sum(`number`) as `number`')
            ->limit(5)
            ->get()
            ->toArray();

        krsort($out);
        $out_data = array();
        foreach ($out as $o) {
            array_push($out_data, $o);
        }

        $complete = TransactionComplete::orderBy('id', 'desc')->where("currency", $currency_id)->where("legal", $legal_id)->take(15)->get();

        $last_price = 0;
        $last = TransactionComplete::orderBy('id', 'desc')->where("currency", $currency_id)->where("legal", $legal_id)->first();
        if (!empty($last)) {
            $last_price = $last->price;
        }

        $user_legal = 0;
        $user_currency = 0;
        if (!empty($user_id)) {
            $legal = UsersWallet::where("user_id", $user_id)->where("currency", $legal_id)->first();
            if ($legal) {
                $user_legal = $legal->change_balance;
            }
            $currency = UsersWallet::where("user_id", $user_id)->where("currency", $currency_id)->first();
            if ($currency) {
                $user_currency = $currency->change_balance;
            }
        }

        $ustd_price = 0;
        $last = TransactionComplete::orderBy('id', 'desc')
            ->where("currency", $legal_id)
            ->where("legal", 1)->first();//4是usdt
        if (!empty($last)) {
            $ustd_price = $last->price;
        }
        if ($legal_id == 1) {
            $ustd_price = 1;
        }
        $cny_price = Currency::getCnyPrice($legal_id);
        return $this->success([
            "in" => $in,
            "out" => $out_data,
            "cny_price" => $cny_price,
            "last_price" => $last_price,
            "user_legal" => $user_legal,
            "user_currency" => $user_currency,
            "complete" => $complete
        ]);
    }
}
