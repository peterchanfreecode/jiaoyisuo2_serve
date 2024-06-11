<?php

namespace App\Console\Commands;

use App\Models\AccountLog;
use App\Models\UserGold;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\UsersWallet;

class Clear_gold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear_gold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结算利息';

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
        Log::info('清理体验金' . Carbon::now()->toDateTimeString());
        $this->info('开始执行清理体验金:' . Carbon::now()->toDateTimeString());
        UserGold::where('status', 1)
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {

                    DB::beginTransaction();
                    try {
                        $info_time = strtotime($order->create_time);
                        if ((time() - $info_time) >= 86400) {
                            $order->status = -1;
                            $order->save();
                            $user_wallet = UsersWallet::where('user_id', $order['user_id'])
                                ->where('currency', 3)->lockForUpdate()->first();
                            if ($user_wallet->change_balance > $order->amount) {
                                $amount = $order->amount;
                            } else {
                                $amount = $user_wallet->change_balance;
                            }
                            change_wallet_balance($user_wallet, 2, -$amount, AccountLog::ADMIN_CHANGE_BALANCE, '扣除体验金');
                            $this->info('清理体验金成功:' . Carbon::now()->toDateTimeString());
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        Log::info('清理体验金失败:', $order);
                        DB::rollBack();
                    }
                }
            });
    }

}
