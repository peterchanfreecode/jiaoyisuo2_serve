<?php

namespace App\Http\Controllers\Api;

use App\Models\NewCurrency;
use App\Models\Setting;
use App\Models\UserMicro;
use App\Models\UsersInsurance;
use App\Service\MutexService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Logic\MicroTradeLogic;
use App\Models\Users;
use App\Models\CurrencyQuotation;
use App\Models\Currency;
use App\Models\MicroSecond;
use App\Models\UsersWallet;
use App\Models\MicroOrder;
use App\Models\MarketHour;
use App\Models\CurrencyMatch;
use App\Models\InsuranceType;
use App\Service\MicroService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MicroOrderController extends Controller
{

    /**
     * 取允许支付的币种
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayableCurrencies()
    {
        $currencies = Currency::with('microNumbers')
            ->where('is_micro', 1)
            ->get();
        $user = Users::getAuthUser();

        $currencies->transform(function ($item, $key) use ($user) {
            // 追加上险种
            $insurance_types = InsuranceType::where('currency_id', $item->id)
                ->get();
            $item->setAttribute('insurance_types', $insurance_types);
            // 追加上用户的钱包
            $wallet = UsersWallet::where('user_id', $user->id)
                ->where('currency', $item->id)
                ->first();
            if ($wallet) {
                $micro_with_insurance = bc_add($wallet->micro_balance, $wallet->insurance_balance);
                $wallet->setAttribute('micro_with_insurance', $micro_with_insurance);
            }
            $item->setAttribute('user_wallet', $wallet);
            // 追加上用户买的保险
            $user_insurance = UsersInsurance::where('user_id', $user->id)
                ->whereHas('insurance_type', function ($query) use ($item) {
                    $query->where('currency_id', $item->id);
                })->where('status', 1)->first();
            $item->setAttribute('user_insurance', $user_insurance);
            return $item;
        });
        return $this->success($currencies);
    }

    /**
     * 取到期时间
     */
    public function getSeconds()
    {
        $panType = DB::table('settings')->where('key', 'pan_type')->value('value');;
        $seconds = MicroSecond::where('status', 1)->where('type', $panType)->get();
        /*    $user_id = Users::getUserId();
            if ($seconds) {
                foreach ($seconds as &$v) {
                    $user_mico_info = UserMicro::where("user_id", $user_id)->where("mic_sec_id", $v->id)->first();
                    $v->profit_ratio_show = $user_mico_info->profit_ratio ?? $v->profit_ratio;
                }
            }*/
        return $seconds->count() > 0 ? $this->success($seconds) : $this->error($seconds);
    }

    /**
     * 下单
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request)
    {

        $user_id = Users::getUserId();
        $type = $request->input('type', 0);
        $match_id = $request->input('match_id', 0);
        $currency_id = 3;
        $seconds = $request->input('seconds', 0);
        $number = $request->input('number', 0);
        $validator = Validator::make($request->all(), [
            'match_id' => 'required|integer|min:1',
            'currency_id' => 'required|integer|min:1',
            'type' => 'required|integer|in:1,2',
            'seconds' => 'required|integer|min:1',
            'number' => 'required|numeric|min:0',
        ], [], [
            'match_id' => '交易对',
            'currency_id' => '支付币种',
            'type' => '下单类型',
            'seconds' => '到期时间',
            'number' => '投资数额',
        ]);

        $info = MutexService::tryGetLock($user_id . $match_id . $seconds . $number, $user_id . $match_id . $seconds . $number, 3000);
        if (!$info) {
           return $this->error("交易状态异常,请勿重复提交");
        }
        try {
            $currency_match = CurrencyMatch::find($match_id);
            //验证新币锁仓时间
            $new_info = NewCurrency::where("currency", $currency_match->currency_id)->first();
            if ($new_info) {
                $lock_end = strtotime($new_info->lock_end);
                if (time() < $lock_end) {
                    return $this->error("币种锁仓中,暂不能交易");
                }
            }
            //进行基本验证
            throw_if($validator->fails(), new \Exception($validator->errors()->first()));
            $currency_quotation = CurrencyQuotation::where('match_id', $match_id)->first();
            throw_unless($currency_quotation, new \Exception('当前未获取到行情'));
            $rkey = 'market.' . strtolower($currency_match->currency_name . $currency_match->legal_name) . '.kline.1min';
            $market = json_decode(Redis::get($rkey), true);
            $market = $market['tick'];
            $price = $market['close'] ?? $currency_quotation->now_price;
            $order_data = [
                'user_id' => $user_id,
                'type' => $type,
                'match_id' => $match_id,
                'currency_id' => $currency_id,
                'seconds' => $seconds,
                'price' => $price,
                'number' => $number,
            ];

            $order = MicroTradeLogic::addOrder($order_data);
            MutexService::releaseLock($user_id . $match_id . $seconds . $number, $user_id . $match_id . $seconds . $number);
            return $this->success($order);
        } catch (\Throwable $th) {
            MutexService::releaseLock($user_id . $match_id . $seconds . $number, $user_id . $match_id . $seconds . $number);
            return $this->error($th->getMessage());
        }
    }

    public function lists(Request $request)
    {
        try {
            $user_id = Users::getUserId();
            $limit = $request->input('limit', 10);
            $status = $request->input('status', -1);
            $match_id = $request->input('match_id', -1);
            $currency_id = $request->input('currency_id', -1);
            $lists = MicroOrder::where('user_id', $user_id)
                ->when($status <> -1, function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->when($match_id <> -1, function ($query) use ($match_id) {
                    $query->where('match_id', $match_id);
                })
                ->when($currency_id <> -1, function ($query) use ($currency_id) {
                    $query->where('currency_id', $currency_id);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit);
            $lists->each(function ($item, $key) {
                return $item->append('remain_milli_seconds');
            });
            return $this->success($lists);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * 获得秒合约下单规则
     */
    protected function getOrderRules($user_id, $currency_id, $user_insurance)
    {
        //默认规则

        $insurance_rules_arr = $user_insurance->insurance_rules_arr;
        if (count($insurance_rules_arr) > 0) {
            foreach ($insurance_rules_arr as $rule) {
                if ($user_insurance->amount >= $rule['amount']) {
                    return $rule;
                }
            }
        }
        return $rule = [
            'place_an_order_max' => 500,
            'existing_number' => 3
        ];
    }

    /**
     * 获得该币种交易中的秒合约订单
     */
    protected function getExistingOrderNumber($user_id, $currency_id)
    {
        $count = MicroOrder::where('user_id', $user_id)
            ->where('status', MicroOrder::STATUS_OPENED)
            ->where('currency_id', $currency_id)
            ->count();
        return $count;
    }

    /**
     * 受保时间段是否可以下单
     */
    protected function canOrder($user_id, $currency_id, $number)
    {
        //$user = Users::getById($user_id);
        //该币种是否购买了保险
        $user_insurance = UsersInsurance::where('user_id', $user_id)
            ->whereHas('insurance_type', function ($query) use ($currency_id) {
                $query->where('currency_id', $currency_id);
            })
            ->where('status', 1)
            ->where('claim_status', 0)
            ->first();
        if (!$user_insurance) {
            return '尚未申购或理赔保险';
        }
        $insurance_type = $user_insurance->insurance_type;
        if ($insurance_type->is_t_add_1 == 1) {
            $user_insurance_created_at_date = Carbon::parse($user_insurance->created_at);
            if (Carbon::today()->isSameAs('Y-m-d', $user_insurance_created_at_date)) {
                return '申购的保险T+1生效';
            }
        }

        //dd($insurance_type);
        //该用户该保险的对应的钱包。
        $user_wallet = UsersWallet::where('user_id', $user_id)
            ->where('currency', $insurance_type->currency_id)
            ->first();

        //受保资产为0不允许下单
        if ($user_wallet->insurance_balance == 0) {
            return '受保资产为零';
        }


        switch ($insurance_type->type) {
            case 1:
                //受保金额小于等于此时不可以下单
                $defective_amount = bc_mul($user_insurance->amount, bc_div($insurance_type->defective_claims_condition, 100));

                //正向险种，受保资产小于等于【条件1额度】，不允许下单
                if ($user_wallet->insurance_balance <= $defective_amount) {
                    return '受保资产小于等于可下单条件';
                }
                break;
            case 2:
                //反向险种，受保资产小于等于【条件2额度】，不允许下单
                if ($user_wallet->insurance_balance <= $insurance_type->defective_claims_condition2) {
                    return '您已超过持仓限制，暂停下单。';
                }
                break;
            default:
                return '未知的险种类型';
        }


        $order_rules = $this->getOrderRules($user_id, $currency_id, $user_insurance);
        //dd($order_rules);
        if ($number > $order_rules['place_an_order_max']) {
            return '超过最大持仓数量限制';
        }

        $getExistingOrderNumber = $this->getExistingOrderNumber($user_id, $currency_id);
        if ($getExistingOrderNumber >= $order_rules['existing_number']) {
            return '交易中的订单大于最大挂单数量';
        }

        return true;//可以下单
    }

    public function get_mico_price(Request $request)
    {

        $id = $request->input('id', "");
        if (!$id) {
            return $this->error("参数错误");
        }
        $info = MicroOrder::where("id", $id)->first();
        if (!$info) {
            return $this->error("订单不存在");
        }

        try {
            $currency_match = CurrencyMatch::find($info->match_id);
            $float_diff = MicroService::diff($currency_match); //产生一个随机浮动价格
            $type = rand(0, 1);
            if ($type == 1) {
                $end_price = bc_sub($info->open_price, $float_diff, 4);
            } else {
                $end_price = bc_add($info->open_price, $float_diff, 4);
            }

            /*   $info->end_price = $end_price;*/
            $end_price = floatval($end_price);
            /*    $info->save();*/
            return $this->success($end_price);
        } catch (\Exception $e) {
            return $this->success(floatval($info->open_price));
        }

    }

    public function get_mico_end(Request $request)
    {
        $id = $request->input('id', "");
        if (!$id) {
            return $this->error("参数错误");
        }
        $order_info = MicroOrder::where("id", $id)->first();
        if (!$order_info) {
            return $this->error("订单不存在");
        }
        if ($order_info->status == 3) {
            return $this->success($order_info);
        }
        $currency_match = CurrencyMatch::find($order_info->match_id);
        $user_id = Users::getUserId();
        $risk_mode = Setting::getValueByKey('risk_mode', 0);

        switch ($risk_mode) {
            case 1:

                MicroService::riskByUser($currency_match, $order_info, $user_id);
                break;
            default:
                MicroService::riskByProbability($currency_match, $order_info);
                break;
        }
        /*  if ($rel) {*/
        $order_info = MicroOrder::where("id", $id)->first();
        return $this->success($order_info);
        /*  } else {
              return $this->error("结算失败");
          }*/

    }

}
