<?php

namespace App\Console\Commands;

use App\Models\AccountLog;
use App\Models\NewCurrencyOrder;
use App\Models\UsersWallet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\RebateEvent;

class NewCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'new_currency_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '新币解冻';

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
        Log::info('新币解冻'. Carbon::now()->toDateTimeString());
        NewCurrencyOrder::whereRaw('status = 2 and is_thaw = 0 and lock_end < ?', [Carbon::now()->toDateString()])
            ->chunkById(50, function ($orders) {
                foreach ($orders as $order) {
                    try {
                        DB::beginTransaction();
                        NewCurrencyOrder::where('id', $order['id'])->update(['is_thaw' => 1]);
                        // 减冻结
                        UsersWallet::decUserWallet($order['uid'], $order['currency'], 'lock_change_balance', $order['get_apply_amount']);
                        $user_wallet = UsersWallet::where('user_id', $order['uid'])
                            ->where('currency', $order['currency'])->lockForUpdate()->first();
                        change_wallet_balance($user_wallet, 2, $order['get_apply_amount'], AccountLog::IEO_UN_THAW, '新币锁仓到期解冻');
                        $price = $user_wallet->usdt_price;
                        $total_money = $price*$order["get_apply_amount"];
                        $money = $total_money-$order["get_coin_amount"];
                        if($money>0){
                            event(new RebateEvent($order['user_id'], $money));//新币盈利返佣
                        }

                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::info('新币解冻失败'.$e->getMessage());
                        Log::info('新币解冻失败'.$e->getTraceAsString());
                    }
                }
            });
    }

}
