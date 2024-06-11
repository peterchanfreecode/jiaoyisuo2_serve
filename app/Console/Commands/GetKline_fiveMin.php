<?php

namespace App\Console\Commands;

use App\Models\CurrencyMatch;
use Illuminate\Console\Command;
use App\Jobs\SendMarket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Service\RedisQuotation;

class GetKline_FiveMin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_kline_data_fivemin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取K线图数据五分钟';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function sendKine($currency, $peroid, $open, $close, $low, $high, $vol, $first_stamp)
    {
        $currency_match = CurrencyMatch::where('currency_id', $currency->currency_id)->where('legal_id', $currency->legal_id)->first();
        $kline_data = [
            'type' => 'kline',
            'period' => $peroid,
            'match_id' => $currency_match->id,
            'currency_id' => $currency->currency_id,
            'currency_name' => $currency->currency_name,
            'legal_id' => $currency->legal_id,
            'legal_name' => $currency->legal_name,
            'open' => $open,
            'close' => $close,
            'low' => $low,
            'high' => $high,
            'symbol' => $currency->currency_name . '/' . $currency->legal_name,
            'volume' => sctonum($vol),
            'time' => $first_stamp * 1000,
        ];
        Log::info($currency->currency_name . "---" . $currency->legal_name);
        $kline_data = RedisQuotation::comparison($currency->currency_name, $currency->legal_name, $peroid, $first_stamp, $kline_data);
        SendMarket::dispatch($kline_data)->onQueue('kline.all');
        RedisQuotation::set_redis_kline($currency->currency_name, $currency->legal_name, $peroid, $first_stamp, $kline_data);
//        RedisQuotation::setData($kline_data);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while (true) {
            date_default_timezone_set('Asia/Shanghai');
            $currencys = CurrencyMatch::where('market_from', 3)->get();
            $peroid = '5min';
            foreach ($currencys as $currency) {
                $currency_name = $currency->currency_name;
                $legal_name = $currency->legal_name;
                $info = RedisQuotation::get_redis_kline_by_zrerange_one($currency_name, $legal_name, $peroid);
                $result = true;
                if ($info) {
                    $time = $info['id'] / 1000;
                    if ($time === Robot::getNowTime($peroid) / 1000 || $time < Robot::getNowTime($peroid) / 1000) {
                        /*if ($time < Robot::getNowTime($peroid) / 1000) {
                            $last_time = strtotime('+5 min', $time);
                        } else {
                            $last_time = $time;
                        }*/
                        $last_time = Robot::getNowTime($peroid) / 1000;
                        $start = $this->getInfo($last_time, strtotime('+5 min', $last_time), $currency_name, $legal_name);

                        $open = sprintf('%.6f', $start['open']);
                        $close = sprintf('%.6f', $start['close']);
                        $high = sprintf('%.6f', $start['high']);
                        $low = sprintf('%.6f', $start['low']);
                        $vol = $start['vol'] ?? $info["vol"];
                        $first_stamp = Robot::getNowTime($peroid, $last_time) / 1000;
                        $this->sendKine($currency, $peroid, $open, $close, $low, $high, $vol, $first_stamp);
                        $this->info('已设置一条信息');
                    } else {
                        $this->info('超出当前时间戳');
                    }
                } else {
                    $result = false;
                }
                if ($result == false) {
                    //第一次
                    //获取1分钟初始
                    $info = RedisQuotation::get_redis_kline_by_range_one($currency_name, $legal_name, "1min");
                    if ($info) {
                        $time = $info['id'] / 1000;
                        $start = $this->getInfo($time, strtotime('+5 min', $time), $currency_name, $legal_name);
                        $open = sprintf('%.6f', $start['open']);
                        $close = sprintf('%.6f', $start['close']);
                        $high = sprintf('%.6f', $start['high']);
                        $low = sprintf('%.6f', $start['low']);
                        $vol = $start['vol'] ?? $info["vol"];
                        $first_stamp = Robot::getNowTime($peroid, $time) / 1000;
                        $this->sendKine($currency, $peroid, $open, $close, $low, $high, $vol, $first_stamp);
                        $this->info('已设置一条信息新增');
                    }
                }
            }

            date_default_timezone_set('America/New_York');
            sleep(2);
        }
    }

    /**
     * 操作redis
     */
    public function getInfo($start, $end, $currency_name, $legal_name)
    {
        $arr = RedisQuotation::get_redis_kline_by_period($currency_name, $legal_name, "1min", $start, $end);
        if ($arr) {
            $data = [];
            $count = count($arr);
            $i = 1;
            $high = $low = [];
            $vol = '';
            foreach ($arr as $v) {
                $info = json_decode($v, true);

                if ($i == 1) {
                    $data["open"] = $info["open"];
                }
                if ($i == $count) {
                    $data["close"] = $info["close"];
                }
                $vol = bcadd($vol, $info["vol"]);
                $high[] = $info["high"];
                $low[] = $info["low"];
                $i++;
            }
            $data["high"] = max($high);
            $data["low"] = min($low);
            $data["vol"] = $vol;
            return $data;
        }

        return false;
    }
}
