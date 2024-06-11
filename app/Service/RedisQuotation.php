<?php


namespace App\Service;
/*use App\Models\RobotQuotation;*/
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
class RedisQuotation
{
    public static function set_redis_kline($baseCurrency, $quoteCurrency, $period, $score, $data)
    {
        $key = "market:kline:" . strtolower($baseCurrency) . '_' . strtolower($quoteCurrency) . "_" . $period;
        $market_data = [
            'id' => $data["time"] ?? $data["id"] * 1000,
            'period' => $data["period"],
            'base-currency' => strtoupper($baseCurrency),
            'quote-currency' => strtoupper($quoteCurrency),
            'open' => sctonum($data["open"]),
            'close' => sctonum($data["close"]),
            'high' => sctonum($data["high"]),
            'low' => sctonum($data["low"]),
            'vol' => sctonum($data["volume"] ?? $data["vol"]),
            'amount' => sctonum($data["amount"] ?? ""),
        ];

        $num = Redis::zcount($key, "-inf", "+inf");
        if ($num > 500) {
            $end = $num - 500;
            Redis::ZREMRANGEBYRANK($key, 0, $end);
        }
        $options = ["WITHSCORES" => true, 'LIMIT' => 1];
        $info = Redis::zrevrangebyscore($key, $score, $score, $options);// 查询出上一次redis 储存的值对比
        if ($info) {
            Redis::zremrangebyscore($key, $score, $score);
        }
        Redis::zadd($key, $score, json_encode($market_data));
    }

    public static function get_redis_kline_by_period_one($baseCurrency, $quoteCurrency, $period, $score)
    {
        $key = "market:kline:" . strtolower($baseCurrency) . '_' . strtolower($quoteCurrency) . "_" . $period;
        $options = ["WITHSCORES" => true, 'LIMIT' => 1];
        $info = Redis::zrevrangebyscore($key, $score, $score, $options);
        if ($info) {
            $arr = array_flip($info);
            $info = json_decode($arr[$score], true);
            return $info;
        }
        return false;
    }

    public static function get_redis_kline_by_period($baseCurrency, $quoteCurrency, $period, $startScore, $endScore)
    {
        $key = "market:kline:" . strtolower($baseCurrency) . '_' . strtolower($quoteCurrency) . "_" . $period;
        $options = ["WITHSCORES" => true];
        $info = Redis::zrangebyscore($key, $startScore, $endScore - 1, $options);
        if ($info) {
            $arr = array_flip($info);
            return $arr;
        }

        return false;
    }

    public static function get_redis_kline_by_range_one($baseCurrency, $quoteCurrency, $period)
    {
        $key = "market:kline:" . strtolower($baseCurrency) . '_' . strtolower($quoteCurrency) . "_" . $period;
        $info = Redis::zrange($key, 0, 0);

        if ($info) {
            $info = json_decode($info[0], true);
            return $info;
        }
        return false;
    }

    //获取最近一条
    public static function get_redis_kline_by_zrerange_one($baseCurrency, $quoteCurrency, $period)
    {
        $key = "market:kline:" . strtolower($baseCurrency) . '_' . strtolower($quoteCurrency) . "_" . $period;
        $info = Redis::Zrevrange($key, 0, 0);
        if ($info) {
            $info = json_decode($info[0], true);
            return $info;
        }
        return false;
    }

    //比价
    public static function comparison($baseCurrency, $quoteCurrency, $period, $score, $data)
    {
        $key = "market:kline:" . strtolower($baseCurrency) . '_' . strtolower($quoteCurrency) . "_" . $period;
        $options = ["WITHSCORES" => true, 'LIMIT' => 1];
        $info = Redis::zrevrangebyscore($key, $score, $score, $options);// 查询出上一次redis 储存的值对比
        if ($info) {
            $arr = array_flip($info);
            $info = json_decode($arr[$score], true);
            if ($info['low'] > 0) {
                bc_comp($info['low'], $data['low']) < 0 && $data['low'] = $info['low'];
            }
            if ($info['high'] > 0) {
                bc_comp($info['high'], $data['high']) > 0 && $data['high'] = $info['high']; //新过来的价格如果不高于原最高价则不更新
            }
            if ($info['vol'] > 0) {
                bc_comp($info['vol'], $data['volume']) > 0 && $data['volume'] = $info['vol'];
            }
        }
        return $data;
    }

    //获取最近一条
    public static function get_redis_kline_all($base_currency, $quote_currency, $peroid, $from, $to)
    {
        $res = [];
        $list = self::get_redis_kline_by_period($base_currency, $quote_currency, $peroid, $from, $to);
        if ($list) {
            foreach ($list as $val) {
                $info = json_decode($val, true);
                $res[] = [
                    'base_currency' => $info['base-currency'],
                    'close' => $info['close'],
                    'high' => $info['high'],
                    'low' => $info['low'],
                    'open' => $info['open'],
                    'id' => $info['id'] / 1000,
                    'quote-currency' => $info['quote-currency'],
                    'time' => $info['id'],
                    'vol' => $info['vol'],
                    'volume' => $info['vol'],
                    'amount' => $info['amount'],
                ];
            }
        }
        return $res;
    }

    public static function set_redis_quotation($base_currency, $quote_currency, $period, $score, $data)
    {
        $key = "market:quotation:" . strtolower($base_currency) . '_' . strtolower($quote_currency) . "_" . $period;
        $options = ["WITHSCORES" => true, 'LIMIT' => 1];
        $info = Redis::zrevrangebyscore($key, $score, $score, $options);// 查询出上一次redis 储存的值对比
        if ($info) {
            Redis::zremrangebyscore($key, $score, $score);
        }
        Redis::zadd($key, $score, json_encode($data));
    }

    public static function get_redis_quotation_by_period_one($baseCurrency, $quoteCurrency, $period, $score)
    {
        $key = "market:quotation:" . strtolower($baseCurrency) . '_' . strtolower($quoteCurrency) . "_" . $period;
        $options = ["WITHSCORES" => true, 'LIMIT' => 1];
        $info = Redis::zrevrangebyscore($key, $score, $score, $options);
        if ($info) {
            $arr = array_flip($info);
            $info = json_decode($arr[$score], true);
            return $info;
        }
        return false;
    }
    //落地到数据库
/*    public static function setData($data)
    {
        //检查数据是否存在
        $count = RobotQuotation::where("period", $data["period"]) ->where("symbol", $data["symbol"]) ->count();
        if($count>500){
            RobotQuotation::where("period", $data["period"])
                ->where("symbol", $data["symbol"])
                ->orderBy('id', 'asc')
                ->limit(1)
                ->delete();
        }
        $period_time = $data["time"] / 1000;
        $info = RobotQuotation::where("period", $data["period"])
            ->where("itime", $period_time)
            ->where("symbol", $data["symbol"])
            ->first();
        if (!$info) {
            $info = new RobotQuotation();
        }
        $info->open = $data["open"];
        $info->high = $data["high"];
        $info->low = $data["low"];
        $info->close = $data["close"];
        $info->vol = $data["volume"];
        $info->base = $data["currency_name"];
        $info->target = $data["legal_name"];
        $info->symbol = $data["symbol"];
        $info->itime = $period_time;
        $info->period = $data["period"];
        $info->save();
        return true;
    }*/
    public static function get_market_1min($symbol)
    {
        $key = "market.$symbol.kline.1min";
        $info = Redis::get($key);
        if ($info) {
            $hh = json_decode($info, true);
            return $hh["vol"] ?? 0;
        }
        return 0;
    }
}