<?php

namespace App\Listeners;

use App\Events\RebateEvent;
use App\Models\AccountLog;
use App\Models\Users;
use App\Models\RebateFlow;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UsersWallet;
class RebateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    private $uid;

    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param \App\Events\MoneyFlowEvent $event
     * @return void
     */
    public function handle(RebateEvent $event)
    {
        $amount = $event->amount;
        $this->uid = $event->uid;
        $user_info = Users::find($this->uid);
        if (!$user_info) {
            return false;
        }
        if (!$user_info->parent_id) {
            return false;
        }
        try {
            DB::beginTransaction();
            $this->rebateParent($user_info, $amount, 1);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    private function rebateParent($user_info, $amount, $i)//返佣5级
    {
        $parent_id = $user_info['parent_id'];
        if ($parent_id && $i <= 3) {
            $parent = Users::find($parent_id);
            $rel = $this->get_level($parent, $i);
            if ($rel) {
                $rate = $this->get_rate($i);
                if ($rate) {
                    $this->rebateFlow($parent["id"], $amount, $rate, $i);
                }
            }
            $i++;
            $result = $this->rebateParent($parent, $amount, $i);
            return $result;
        } else {
            return [];
        }
    }

    private function get_level($parent, $i)//判断用户时候满足返佣条件
    {
        $lower_num = 0;//有效下级人数配置
        switch ($i) {
            case 1 :
                $lower_num = Setting::getValueByKey("one_level_rebate_lower_num");
                break;
            case 2 :
                $lower_num = Setting::getValueByKey("two_level_rebate_lower_num");
                break;
            case 3 :
                $lower_num = Setting::getValueByKey("three_level_rebate_lower_num");

                break;
            default:
                $lower_num = 0;
        }
        if ($lower_num == 0 ) {
            return false;
        }
        if ($parent["real_teamnumber"] >= $lower_num) {
            return true;
        }
        return false;
    }

    private function get_rate($i)
    {
        switch ($i) {
            case 1 :
                $rate = Setting::getValueByKey("one_level_rebate_rate");
                break;
            case 2 :
                $rate = Setting::getValueByKey("two_level_rebate_rate");
                break;
            case 3 :
                $rate = Setting::getValueByKey("three_level_rebate_rate");
                break;
            default:
                $rate = 0;
        }
        return $rate;
    }

    private function rebateFlow($receive_user_id, $amount, $rate, $i)//返佣记录
    {
        $rebate_amount = bcmul($amount, bcdiv($rate, 100), 2);
        $model = new RebateFlow();
        $model->order_amount = $amount;
        $model->receive_user_id = $receive_user_id;
        $model->user_id = $this->uid;
        $model->rate = $rate;
        $model->rebate_amount = $rebate_amount;
        $model->level = $i;
        $model->save();
        $wallet = UsersWallet::where('currency', 3)
            ->where('user_id', $receive_user_id)
            ->first();
        if($wallet){
            change_wallet_balance($wallet, 2, $rebate_amount, AccountLog::REBATE, '消费返佣');
        }

    }
}
