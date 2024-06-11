<?php

namespace App\Console\Commands;

use App\Models\CurrencyMatch;
use App\Jobs\SendMarket;
use App\Models\CurrencyQuotation;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Jobs\UpdateCurrencyPrice;
use App\Service\RedisQuotation;

class Futures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'futures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匹配期货';

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

        $this->info('开始期货交易:' . now()->toDateTimeString());
        while (true) {
            $infos = CurrencyMatch::where("market_from", 4)->get();
            if ($infos) {
                foreach ($infos as $v) {
                    $this->waihui($v);
                }
            }

            sleep(10);
        }

        $this->info('期货交易结束:' . now()->toDateTimeString());
    }

    protected function waihui($v)
    {
        $procode = strtolower($v["currency_name"]);
        $arr = [1 => "1min", 5 => "5min", 15 => "15min", 30 => "30min", 60 => "60min", 1440 => "1day"];
        foreach ($arr as $key => $val) {
            $res = $this->curl_get("http://www.11shuju.com/api/wc10/demo/jkline.aspx?period=" . $key . "&rows=1&symbol=" . $procode, true);
            $res = json_decode($res, 1);
            $vol = RedisQuotation::get_market_1min($procode);
            $vol = $vol + rand(20, 80);
            if ($res && $res["success"]) {
                $kline_data = [
                    'type' => 'kline',
                    "period" => $val,
                    'match_id' => $v->id,
                    'currency_id' => $v->currency_id,
                    'currency_name' => $v->currency_name,
                    'legal_id' => $v->legal_id,
                    'legal_name' => $v->legal_name,
                    "open" => $res["results"][0]["open"],
                    "close" => $res["results"][0]["close"],
                    "low" => $res["results"][0]["low"],
                    "high" => $res["results"][0]["high"],
                    "symbol" => $v->currency_name . '/' . $v->legal_name,
                    "volume" => $vol,
                    "time" => $res["results"][0]["start"] * 1000,
                ];
                RedisQuotation::set_redis_kline($v->currency_name, $v->legal_name, $val, $res["results"][0]["start"], $kline_data);
                if ($val == "1min") {
                    UpdateCurrencyPrice::dispatch($kline_data)->onQueue('update_currency_price');
                    $key = "market.{$procode}.kline.1min";
                    $data = ['ch' => $key, 'ts' => $res["results"][0]["start"] * 1000, 'tick' => [
                        'id' => $res["results"][0]["start"],
                        'open' => $res["results"][0]["open"],
                        'close' => $res["results"][0]["close"],
                        'low' => $res["results"][0]["low"],
                        'high' => $res["results"][0]["high"],
                        'vol' => $vol,
                        'amount' => $vol,
                    ]];
                    $hh = json_encode($data);
                    Redis::set($key, $hh);
                }
                if ($val == "1day") {

                    $kline_data["change"] = $this->calcIncreasePair($kline_data);
                    CurrencyQuotation::getInstance($v->legal_id, $v->currency_id)
                        ->updateData([
                            'change' => $kline_data["change"],
                            'now_price' => $res["results"][0]["close"],
                            'volume' => sctonum($vol),
                        ]);
                }
                SendMarket::dispatch($kline_data)->onQueue('kline.all');
                $depth_data = [
                    'type' => 'market_depth',
                    "symbol" => $v->currency_name . '/' . $v->legal_name,
                    'base-currency' => $v->currency_name,
                    'quote-currency' => $v->legal_name,
                    'currency_id' => $v->currency_id,
                    'currency_name' => $v->currency_name,
                    'legal_id' => $v->legal_id,
                    'legal_name' => $v->legal_name,
                    'bids' => $this->get_depth($v, $res["results"][0]["open"]), //买入盘口
                    'asks' => $this->get_depth($v, $res["results"][0]["open"]), //卖出盘口
                ];
                SendMarket::dispatch($depth_data)->onQueue('market.depth');//推送虚拟交易*/
            }

        }
    }

    protected function curl_get($url, $gzip = false)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HEADER, false);

        // 使用gzip压缩传输数据
        if ($gzip) {
            curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        }

        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }

    protected function calcIncreasePair($kline_data)
    {
        $open = $kline_data['open'];
        $close = $kline_data['close'];
        $change_value = bcsub($close, $open, 8);
        $change = bcmul(bcdiv($change_value, $open, 8), 100, 4);
        bccomp($change, 0) > 0 && $change = '+' . $change;
        return $change;
    }

    protected function get_depth($info, $price)
    {
        $faker = Factory::create();
        $arr = [];
        for ($num = 10; $num--; $num > 0) {
            $vol = floatval(sprintf('%.2f', $faker->randomFloat(2, 50, 200)));
            $price = $price + floatval(sprintf('%.2f', $faker->randomFloat(2, $info->fluctuate_min, $info->fluctuate_max)));
            $arr[] = [$price, $vol];
        }
        return $arr;
    }
}
