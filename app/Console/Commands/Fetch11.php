<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendMarket;
use App\Jobs\UpdateCurrencyPrice;
use App\Models\CurrencyQuotation;
use Illuminate\Support\Facades\DB;

class Fetch11 extends Command
{
      /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch11';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '采集';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle()
    {
        // $url = "http://us1688.api.11shuju.com/api/wc10/vipw1a2o795ys1b1688/jkline.aspx?period=1440&symbol=XAGUSD&rows=1";
        // $res = file_get_contents($url);
        // var_dump($res);
        // return;
        
        while(true) {
            
            $rows = DB::table('currency_matches')->where('market_from', 4)->where('is_display', 1)->get(['id', 'currency_id', 'legal_id'])->toArray();
            foreach ($rows as $k => $v) {
                $currency_id = $v->currency_id;
                $match_id = $v->id;
                $currency = DB::table('currency')->where('id', $currency_id)->get(['id', 'name'])->first();
                if (!$currency) continue;
                $currency_name = $currency->name;
                // var_dump($currency_name); continue;
                $timeArr = [1, 5, 30, 60, 1440];
                $minArr = ['1min', '5min', '30min', '60min',  '1day'];
                for($i=0; $i<5; $i++) {
                    $url = 'http://us1688.api.11shuju.com/api/wc10/vipw1a2o795ys1b1688/jkline.aspx?period='.$timeArr[$i].'&symbol='.$currency_name.'&rows=1';
                    // var_dump($url); continue;
                    $res = file_get_contents($url);
                    $res = json_decode($res, true);
                    $res = $res['results'][0];
                    //   var_dump($url); continue;
                    $legal_id = $v->legal_id;
                    
                    $depth_data = [
                      'type' => 'kline',
                      'period' => $minArr[$i],
                      'match_id' => $match_id,
                      'currency_id' => $currency_id,
                      'currency_name' => $currency_name,
                      'legal_id' => $legal_id,
                      'legal_name' => 'USDT',
                      'open' => sctonum($res['open']),
                      'close' => sctonum($res['close']),
                      'high' => sctonum($res['high']),
                      'low' => sctonum($res['low']),
                      'symbol' => $currency_name . '/USDT',
                      'volume' => $res['vol'],
                      'time' => $res['start'] * 1000,
                    ];
            
                    SendMarket::dispatch($depth_data)->onQueue('kline.all');
                    $change_value = bc_sub($res['close'], $res['open']);
                    $change = bc_mul(bc_div($change_value, $res['open']), 100, 2);
                
                    $depth_data['type'] = 'daymarket';
                    $depth_data['change'] = $change;
                    $depth_data['now_price'] = $res['price'];
                    if ($i != 0) continue;
                    $url = "http://us1688.api.11shuju.com/api/wc10/vipw1a2o795ys1b1688/jquotes.aspx?symbol=".$currency_name;
                    $res = file_get_contents($url);
                    $res = json_decode($res, true);
                    $res = $res['results'][0];
                    $depth_data['volume'] = $res['vol'];
                    // var_dump($res); continue;
                    SendMarket::dispatch($depth_data)->onQueue('kline.all');
                    UpdateCurrencyPrice::dispatch($depth_data)->onQueue('update_currency_price');
                    CurrencyQuotation::getInstance($depth_data['legal_id'], $depth_data['currency_id'])
                                    ->updateData([
                                        'change' => $depth_data['change'],
                                        'now_price' => $depth_data['close'],
                                        'volume' => $depth_data['volume'],
                                    ]);
                }
            }
            sleep(3);
        }
    
    }
}