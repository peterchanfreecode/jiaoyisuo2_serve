<?php

namespace App\Console\Commands;

use App\Models\AccountLog;
use App\Models\DepositOrder;
use App\Models\UsersWallet;
use App\Models\DepositOrderInterest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\RebateEvent;

class OrderInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order_interest';

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
        Log::info('利息结算' . Carbon::now()->toDateTimeString());
        $now = Carbon::now()->subDay()->toDateString();
        DepositOrder::whereRaw('status = 1 and start_at <= ? and end_at >= ?', [$now, $now])
            ->chunkById(100, function ($orders) use ($now) {
                foreach ($orders as $order) {
                    DB::beginTransaction();
                    try {
                        $interest = bcmul($order['amount'], ($order['day_rate'] / 100), 8);
                        $orderInterest = [
                            'user_id' => $order['user_id'],
                            'order_id' => $order['id'],
                            'interest' => $interest
                        ];
                        $model = new DepositOrderInterest();
                        $model->forceFill($orderInterest)->save(); // 记录每日利息
                        if($order->type ==1 ){
                            $user_wallet = UsersWallet::where('user_id', $order['user_id'])
                                ->where('currency', $order['currency'])->lockForUpdate()->first();
                            change_wallet_balance($user_wallet, 2, $interest, AccountLog::LH_LOAN, '质押每日结算利息');
                        }
                        if ($now == $order['end_at']) { // 结束
                            self::overTime($order);
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        Log::info('结算利息失败:' . $e->getMessage());
                        Log::info('结算利息失败:' . $e->getTraceAsString());
                        DB::rollBack();
                    }
                }
            });
    }

    // 到期
    public static function overTime($order)
    {
        Log::info('到期结算：订单信息：ID：' . $order['id'] . ' -- 用户ID：' . $order['user_id'] . ' -- 币种：' . $order['currency'] . ' -- 本金：' . $order['amount'] . ' -- 利息：' . $order['total_interest']);
        DepositOrder::where('id', $order['id'])->update(['status' => 2]); // 订单已完成
        $user_wallet = UsersWallet::where('user_id', $order['user_id'])
            ->where('currency', $order['currency'])->lockForUpdate()->first();
        event(new RebateEvent($order['user_id'], $order['total_interest']));//质押利息返佣
        // 减冻结
        UsersWallet::decUserWallet($order['user_id'], $order['currency'], 'lock_change_balance', $order['amount']);
        change_wallet_balance($user_wallet, 2, $order['amount'], AccountLog::LH_LOAN, '质押到期,归还本金');
        if ($order['type'] != 1) {
            change_wallet_balance($user_wallet, 2, $order['total_interest'], AccountLog::LH_LOAN, '质押到期,利息');
        }

    }
}
