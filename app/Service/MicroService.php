<?php

namespace App\Service;

use App\Models\AccountLog;
use App\Models\Setting;
use App\Models\Users;
use App\Models\MicroOrder;
use App\Models\UsersWallet;
use Illuminate\Support\Carbon;
use App\Utils\Probability;
use App\Events\RebateEvent;

class MicroService
{
    public static function riskByUser($currency_match, $order_info, $user_id)
    {
        $info = Users::find($user_id);
        if (!$info) {
            return false;
        }

        // $order_info->pre_profit_result = $info->risk;
        $float_diff = self::diff($currency_match);

        try {
            if ($order_info->pre_profit_result == 1) {
                $order_info->refresh();
                if ($order_info->type == MicroOrder::TYPE_RISE) {

                    $order_info->end_price = bc_add($order_info->open_price, $float_diff, 8);
                } elseif ($order_info->type == MicroOrder::TYPE_FALL) {
                    $order_info->end_price = bc_sub($order_info->open_price, $float_diff, 8);
                }
                $order_info->save();
                return self::close($order_info->id);
            } elseif ($order_info->pre_profit_result == -1) {
                $order_info->refresh();
                if ($order_info->type == MicroOrder::TYPE_RISE) {
                    $order_info->end_price = bc_sub($order_info->open_price, $float_diff, 8);
                } elseif ($order_info->type == MicroOrder::TYPE_FALL) {
                    $order_info->end_price = bc_add($order_info->open_price, $float_diff, 8);
                }
                $order_info->save();
                return self::close($order_info->id);
            } else if ($order_info->pre_profit_result == 2) {//买涨赢 买跌输
                $order_info->refresh();
                $order_info->end_price = bc_add($order_info->open_price, $float_diff, 8);
                $order_info->save();
                return self::close($order_info->id);
            } else if ($order_info->pre_profit_result == 3) {//买跌赢 买涨输
                $order_info->refresh();
                $order_info->end_price = bc_sub($order_info->open_price, $float_diff, 8);
                $order_info->save();
                return self::close($order_info->id);
            } else {

                return self::riskByProbability($currency_match, $order_info);
            }
        } catch (\Exception $e) {
            return false;
        }

    }

    public static function riskByProbability($currency_match, $order_info)
    {
        //当前盈利概率
        $risk_profit_probability = 60;
        $risk_loss_probability = 100 - $risk_profit_probability;
        $probability_data = [
            [
                'pre_profit_result' => 1,
                'chance' => $risk_profit_probability,
            ],
            [
                'pre_profit_result' => -1,
                'chance' => $risk_loss_probability,
            ]
        ];
        try {
            $profit_result = Probability::lotteryRaffle($probability_data);

            $float_diff = self::diff($currency_match);
            $order_info->pre_profit_result = $profit_result['pre_profit_result'];
            if ($profit_result['pre_profit_result'] == -1) {
                $order_info->refresh();
                if ($order_info->type == MicroOrder::TYPE_RISE) {
                    $order_info->end_price = bc_add($order_info->open_price, $float_diff, 8);
                } elseif ($order_info->type == MicroOrder::TYPE_FALL) {
                    $order_info->end_price = bc_sub($order_info->open_price, $float_diff, 8);
                }
                $order_info->save();
            } elseif ($profit_result['pre_profit_result'] == 1) {
                $order_info->refresh();
                if ($order_info->type == MicroOrder::TYPE_RISE) {
                    $order_info->end_price = bc_sub($order_info->open_price, $float_diff, 8);
                } elseif ($order_info->type == MicroOrder::TYPE_FALL) {
                    $order_info->end_price = bc_add($order_info->open_price, $float_diff, 8);
                }
            }
            $order_info->save();
            return self::close($order_info->id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function diff($currency_match)
    {
        $faker = \Faker\Factory::create();
        $decimal = 0;
        if (stripos($currency_match->fluctuate_min, '.') !== false) {
            $fluctuate_min = rtrim($currency_match->fluctuate_min, '0'); //移除掉小数点后面右侧多余的0
            $fluctuate_min = rtrim($fluctuate_min, '.'); //如果是整数再移除掉小数点
            $decimal_index = stripos($fluctuate_min, '.'); //查找小数点的位置
            if ($decimal_index !== false) {
                $decimal = strlen($fluctuate_min) - $decimal_index - 1;
            }
        }
        trim($currency_match->fluctuate_min, '0');
        $float_diff = $faker->randomFloat($decimal, $currency_match->fluctuate_min, $currency_match->fluctuate_max); //产生一个随机浮动价格
        return $float_diff;
    }

    public static function close($order_id)
    {
        $info = MicroOrder::find($order_id);
        if ($info->status == 3) {
            return false;
        }

        $info->append('profit_type');
        $key = 'microOrder_' . $order_id;
        MutexService::tryGetLock($key, $order_id, 3000);
        // 根据盈亏生成相关参数
        if ($info->profit_type == 1) {
            //结算本金和利息
            $profit_ratio = bc_div($info->profit_ratio, 100);
            $capital = $info->number;
            $fact_profit = bc_mul($capital, $profit_ratio, 4);
            $change = bc_add($capital, $fact_profit, 4);
            event(new RebateEvent($info['user_id'], $fact_profit));//秒合约盈利返佣返佣
            $memo = '秒合约订单平仓,盈利结算';
        } elseif ($info->profit_type == 0) {
            //结算本金,利息为0
            $capital = $info->number;
            $fact_profit = 0;
            $change = $capital;
            $memo = '秒合约订单平仓结算,平局结算';
        } elseif ($info->profit_type == -1) {
            //本金填补亏损
            $lose_ratio = bc_div($info->lose_ratio, 100);
            $capital = $info->number;
            $change = 0;
            $fact_profit = -bc_mul($capital, $lose_ratio, 4);
            if ($fact_profit < $capital) {
                $change = bc_add($capital, $fact_profit, 4);
            }
            $memo = '秒合约订单,亏损结算';
        }
        $info->profit_result = $info->profit_type;
        $info->status = MicroOrder::STATUS_CLOSED;
        $info->fact_profits = $fact_profit;
        $info->complete_at = Carbon::now();
        $info->save();
        $wallet = UsersWallet::where('currency', $info->currency_id)
            ->where('user_id', $info->user_id)
            ->first();
        $balance_type = 2;
        change_wallet_balance($wallet, $balance_type, $change, AccountLog::MICRO_TRADE, $memo, false, 0, 0, '订单号' . $info->id, true);
        MutexService::releaseLock($key, $order_id);
        return true;
    }
}