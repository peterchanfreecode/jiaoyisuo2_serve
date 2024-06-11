<?php

namespace App\Console\Commands;

use App\Models\CurrencyMatch;
use App\Jobs\SendMarket;
use App\Models\MarketHour;

use Faker\Factory;
use Illuminate\Console\Command;
use App\Models\Robot as RobotModel;
use Illuminate\Support\Facades\Redis;
use App\Jobs\UpdateCurrencyPrice;
use App\Service\RedisQuotation;

class Robot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'robot {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匹配交易自动挂单机器人';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('--------------------------------------------------');
        $this->info('开始执行机器人:' . now()->toDateTimeString());

        $id = $this->argument('id');

        while (true) {
            $robot = RobotModel::find($id);

            if (!$robot) {
                $this->info('找不到此机器人');
                break;
            }

            if ($robot->status == RobotModel::STOP) {
                $this->info('机器人已关闭');
                break;
            }

            $this->info('当前交易对是:' . $robot->currency_info . '/' . $robot->legal_info);
            $this->info('当前数量区间:' . $robot->number_min . '-' . $robot->number_max);

            try {
                if ($robot->sell == RobotModel::OPEN) {
                    $this->info('模拟数据');
                    $this->sell($robot);

                }
            } catch (\Exception $e) {
                $this->info($e->getMessage());
            }
            $this->info('睡眠时间：' . $robot->second);
            sleep($robot->second);
        }

        $this->info('机器人执行结束:' . now()->toDateTimeString());
        $this->info('--------------------------------------------------');
    }

    protected function sell($robot)
    {
        $this->SaveQuotation($robot);

        $score = self::getNowTime() / 1000;
        $price = RedisQuotation::get_redis_kline_by_period_one($robot->currency_info, $robot->legal_info, "1min", $score);
        $currency_name = strtolower($robot->currency_info . 'usdt');
        $key = "market.{$currency_name}.kline.1min";
        $data = ['ch' => $key, 'ts' => $score * 1000, 'tick' => [
            'id' => $score,
            'open' => $price['open'],
            'close' => $price['close'],
            'low' => $price['low'],
            'high' => $price['high'],
            'vol' => $price['vol'],
            'amount' => $price['vol'],
            'count' => rand($robot->number_min, $robot->number_max)
        ]];
        $hh = json_encode($data);
        Redis::set($key, $hh);
    }

    public function SaveQuotation($robot)
    {
        $time = self::getNowTime();
        $last_time = strtotime('-1 min', $time / 1000);
        $last_info = RedisQuotation::get_redis_kline_by_period_one($robot->currency_info, $robot->legal_info, "1min", $last_time);
        $info = RedisQuotation::get_redis_kline_by_period_one($robot->currency_info, $robot->legal_info, "1min", $time / 1000);
        $huobi_info = $this->getPriceHuobi($robot, $last_info, $info);
        if (!$huobi_info) {
            return false;
        }
        $now_info = RedisQuotation::get_redis_quotation_by_period_one($robot->currency_info, $robot->legal_info, "1min", $time / 1000);//是否定义了行情
        $needle = [];
        $needle['open'] = $huobi_info["open"];

        if ($now_info) {
            $needle['close'] = $now_info['close'];
            $needle['high'] = $now_info['high'];
            $needle['low'] = $now_info['low'];
            $needle['vol'] = $now_info['vol'];
        } else {
            $needle['close'] = $huobi_info['close'];
            $needle['high'] = $huobi_info['high'];
            $needle['low'] = $huobi_info['low'];
            $needle['vol'] = $huobi_info["vol"];
        }
        $currency_match = CurrencyMatch::where('currency_id', $robot->currency_id)->where('legal_id', $robot->legal_id)->first();
        $kline_data = [
            'type' => 'kline',
            'period' => "1min",
            'match_id' => $currency_match->id,
            'currency_id' => $robot->currency_id,
            'currency_name' => $robot->currency_info,
            'legal_id' => $robot->legal_id,
            'legal_name' => $robot->legal_info,
            'open' => $needle['open'],
            'close' => $needle['close'],
            'low' => $needle['low'],
            'high' => $needle['high'],
            'symbol' => $robot->currency_info . '/' . $robot->legal_info,
            'volume' => sctonum($needle['vol']),
            'time' => $time,
        ];

        $kline_data = RedisQuotation::comparison($robot->currency_info, $robot->legal_info, "1min", $time / 1000, $kline_data);
        RedisQuotation::set_redis_kline($robot->currency_info, $robot->legal_info, "1min", $time / 1000, $kline_data);
        SendMarket::dispatch($kline_data)->onQueue('kline.all');
        UpdateCurrencyPrice::dispatch($kline_data)->onQueue('update_currency_price');
        $depth_data = [
            'type' => 'market_depth',
            'symbol' => $robot->currency_info . '/' . $robot->legal_info,
            'base-currency' => $robot->currency_info,
            'quote-currency' => $robot->legal_info,
            'currency_id' => $robot->currency_id,
            'currency_name' => $robot->currency_info,
            'legal_id' => $robot->legal_id,
            'legal_name' => $robot->legal_info,
            'bids' => $this->get_depth($robot,$needle['open']), //买入盘口
            'asks' =>  $this->get_depth($robot,$needle['open']), //卖出盘口
        ];
        SendMarket::dispatch($depth_data)->onQueue('market.depth');//推送虚拟交易
        /*   RedisQuotation::setData($kline_data);*/
    }

    public function get_depth($robot,$price)
    {
        $faker = Factory::create();
        $arr=[];
        for($num=10;$num--;$num>0){
            $vol = floatval(sprintf('%.2f', $faker->randomFloat(2, $robot->number_min, $robot->number_max)));
            $price = $price+floatval(sprintf('%.2f', $faker->randomFloat(2, $robot->float_number_down, $robot->float_number_up)));
            $arr[]= [$price,$vol];
        }
        return $arr;
    }

    public function getPriceHuobi($robot, $last_info, $info)
    {
        //获取最新价格
        $symbol = strtolower($robot->huobi_currency . 'usdt');
        $url = "https://api.huobi.pro/market/history/kline?symbol={$symbol}&period=1min&size=1";
        $con = json_decode(file_get_contents($url), true);
        if (!is_array($con)) {
            return false;
        }
        $obj = $con['data'][0];
        $change = $this->calcIncreasePair($obj['open'], $obj['close']);
        $high_change = $this->calcIncreasePair($obj['open'], $obj['high']);
        $low_change = $this->calcIncreasePair($obj['open'], $obj['low']);
        $faker = Factory::create();
        if ($last_info) {
            if ($info) {
                $obj['open'] = $info['open'];
                $obj['high'] = floatval(sprintf('%.6f', $info['open'] * (1 + $high_change)));
                $obj['low'] = floatval(sprintf('%.6f', $info['open'] * (1 + $low_change)));
                $obj['close'] = floatval(sprintf('%.6f', $info['open'] * (1 + $change)));
                $vol = floatval(sprintf('%.2f', $faker->randomFloat(2, $robot->number_min, $robot->number_max)));
                if ($vol > $info['vol']) {
                    $obj['vol'] = $vol;
                } else {
                    $obj['vol'] = $info['vol'];
                }

            } else {
                $obj['open'] = $last_info['close'];
                $obj['high'] = floatval(sprintf('%.6f', $last_info['close'] * (1 + $high_change)));
                $obj['low'] = floatval(sprintf('%.6f', $last_info['close'] * (1 + $low_change)));
                $obj['close'] = floatval(sprintf('%.6f', $last_info['close'] * (1 + $change)));
                $obj['vol'] = floatval(sprintf('%.2f', $faker->randomFloat(2, $robot->number_min, $robot->number_max)));
            }

        } else {
            $url = "https://api.huobi.pro/market/history/kline?symbol={$symbol}&period=1day&size=1";
            $con = json_decode(file_get_contents($url), true);
            if (!is_array($con)) {
                return false;
            }
            $obj = $con['data'][0];
            $change = $this->calcIncreasePair($obj['open'], $obj['close']);
            $high_change = $this->calcIncreasePair($obj['open'], $obj['high']);
            $low_change = $this->calcIncreasePair($obj['open'], $obj['low']);
            if ($info) {
                $obj['open'] = $info['open'];
                $obj['high'] = floatval(sprintf('%.6f', $info['open'] * (1 + $high_change)));
                $obj['low'] = floatval(sprintf('%.6f', $info['open'] * (1 + $low_change)));
                $obj['close'] = floatval(sprintf('%.6f', $info['open'] * (1 + $change)));
                $vol = floatval(sprintf('%.2f', $faker->randomFloat(2, $robot->number_min, $robot->number_max)));
                if ($vol > $info['vol']) {
                    $obj['vol'] = $vol;
                } else {
                    $obj['vol'] = $info['vol'];
                }
            } else {
                $obj['open'] = floatval(sprintf('%.6f', $robot->currency_price));
                $obj['high'] = floatval(sprintf('%.6f', $robot->currency_price * (1 + $high_change)));
                $obj['low'] = floatval(sprintf('%.6f', $robot->currency_price * (1 + $low_change)));
                $obj['close'] = floatval(sprintf('%.6f', $robot->currency_price * (1 + $change)));
                $obj['vol'] = floatval(sprintf('%.2f', $faker->randomFloat(2, $robot->number_min, $robot->number_max)));
            }
        }
        return $obj;

    }

    protected function calcIncreasePair($open, $close)
    {
        $change_value = bcsub($close, $open);
        $change = bcdiv($change_value, $open, 6);
        return $change;
    }

    /**
     * 获取整数时间
     */
    public static function getNowTime($type = '1min', $time = null)
    {
        date_default_timezone_set('Asia/Shanghai');
        $current = is_null($time) ? time() : $time;

        $yl = 60;
        if ($type == '5min') {
            $yl = 300;
        }
        if ($type == '15min') {
            $yl = 900;
        }
        if ($type == '30min') {
            $yl = 1800;
        }
        if ($type == '60min') {
            $yl = 3600;
        }

        $stamp = ($current % $yl) > 0 ? ($current - $current % $yl) : $current;

        if ($type == '1day') {
            $stamp = strtotime(date('Y-m-d', $current));
        }
        if ($type == '1week') {
            $stamp = strtotime('next Sunday', $current) - 60 * 60 * 24 * 7;
        }
        if ($type == '1mon') {
            $stamp = strtotime(date('Y-m', $current) . '-01');
        }
        date_default_timezone_set('America/New_York');
        return $stamp * 1000;
    }

    /**获取买入卖出随机数
     *
     * @param $number_min
     * @param $number_max
     *
     * @return float
     */
    public function getNumber($number_min, $number_max)
    {
        $faker = Factory::create();
        $num = $faker->randomFloat(2, $number_min, $number_max);
        unset($faker);
        return $num;
    }

}
