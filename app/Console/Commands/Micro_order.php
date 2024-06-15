<?php

namespace App\Console\Commands;

use App\Models\CurrencyMatch;
use App\Models\Setting;
use App\Service\MicroService;
use Illuminate\Console\Command;
use App\Models\MicroOrder;
class Micro_order extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'micro_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '秒合约未完成结算订单人工结算';

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
            $this->info('开始执行结算:' . now()->toDateTimeString());
            $order = MicroOrder::where("status", "<", 3)->get();
            if ($order) {
                foreach ($order as $k => $v) {
                    $c_time = time() - strtotime($v->created_at);
                    if ($c_time > ($v->seconds+30)) {//执行结算
                        self::close($v);
                    }
                }
            }
            $this->info('结算执行结束:' . now()->toDateTimeString());
            $this->info('--------------------------------------------------');
            sleep(1);


    }

    public static function close($v)
    {
        $order_info = MicroOrder::where("id", $v["id"])->first();
        $currency_match = CurrencyMatch::find($order_info->match_id);
        $risk_mode = Setting::getValueByKey('risk_mode', 0);
        switch ($risk_mode) {
            case 1:
                MicroService::riskByUser($currency_match, $order_info, $order_info->user_id);
                break;
            default:
                 MicroService::riskByProbability($currency_match, $order_info);
                break;
        }
    }


}
