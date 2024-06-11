<?php

namespace App\Listeners;

use App\Events\RechargeEvent;
use App\Models\Setting;
use App\Models\Users;
use App\Models\UsersWallet;
use App\Models\AccountLog;
class RechargeListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param RechargeEvent $event
     * @return void
     */
    public function handle(RechargeEvent $event)
    {
        $user_id = $event->userId;
        $type = $event->type;
        $user_info = Users::find($user_id);
        if (!$user_info) {
            return false;
        }
        if (!$user_info->parent_id) {
            return false;
        }
        if($type ==1 ){
            if($user_info->is_charge == 1){//充值过的用户不在计算有效人数
                return false;
            }
            $user_info->first_consumption_time = date("Y-m-d H:i:s");
            $user_info->is_charge = 1;
            $user_info->save();
            $this->parent($user_info, 1);
        }else{
            if($user_info->is_buy_new_currency == 1){//购买过新币不在计算新币人数
                return false;
            }
            $user_info->is_buy_new_currency = 1;
            $user_info->save();
            $this->buy_currery($user_info, 1);
        }

    }

    private function parent($user_info, $i)
    {
        $parent_id = $user_info['parent_id'];
        if ($parent_id && $i <= 3) {
            $parent = Users::find($parent_id);
            $parent->real_teamnumber = $parent->real_teamnumber + 1;
            if ($i == 1) {
                //直属上级增加邀请奖励
                $munber = $parent->zhitui_real_number+1;
                $money = $this->get_invite($munber);//算出奖励区间
                if($money>0 ){
                    $wallet = UsersWallet::where('currency', 3)
                        ->where('user_id', $parent_id)
                        ->first();
                    change_wallet_balance($wallet, 2, $money, AccountLog::INVITE, '邀请好友奖励');
                    $wallet = UsersWallet::where('currency', 3)
                        ->where('user_id', $user_info['id'])
                        ->first();
                    change_wallet_balance($wallet, 2, $money, AccountLog::INVITE, '注册消费奖励');
                }

                $parent->zhitui_real_number = $parent->zhitui_real_number + 1;//直属加1
            }
            $parent->save();
            $i++;
            $result = $this->parent($parent, $i);
            return $result;
        } else {
            return [];
        }
    }
    private function buy_currery($user_info, $i)
    {
        $parent_id = $user_info['parent_id'];
        if ($parent_id && $i <= 3) {
            $parent = Users::find($parent_id);
            $parent->new_currery_number = $parent->new_currery_number + 1;
            $parent->save();
            $i++;
            $result = $this->buy_currery($parent, $i);
            return $result;
        } else {
            return [];
        }
    }
    private function get_invite($num)
    {
        $min1 = Setting::getValueByKey("invite_0_min");
        $max1 = Setting::getValueByKey("invite_0_max");
        $min2 = Setting::getValueByKey("invite_1_min");
        $max2 = Setting::getValueByKey("invite_1_max");
        $min3 = Setting::getValueByKey("invite_2_min");
        $max3 = Setting::getValueByKey("invite_2_max");
        $min4 = Setting::getValueByKey("invite_3_min");
        $max4 = Setting::getValueByKey("invite_3_max");
        if($min1<=$num && $num<=$max1){
            $money = Setting::getValueByKey("invite_0_price");
        }else if($min2<=$num && $num<=$max2){
            $money = Setting::getValueByKey("invite_1_price");
        }else if($min3<=$num && $num<=$max3){
            $money = Setting::getValueByKey("invite_2_price");
        }else if($min4<=$num && $num<=$max4){
            $money = Setting::getValueByKey("invite_3_price");
        }else{
            $money = 0;
        }
        return  $money;
    }
}
