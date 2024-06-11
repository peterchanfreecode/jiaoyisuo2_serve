<?php


namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\Users;
use App\Models\UsersWallet;
use Illuminate\Console\Command;

class UpdateFund extends Command
{
    protected $signature = 'update_user_fund';
    protected $description = '更新资产';

    public function handle()
    {

        $this->comment("start");
        $user = Users::all();
        if (count($user) != 0) {
            foreach ($user as $v) {
                $fund = $this->fund($v->id);
                $v->fund = $fund;
                $v->save();
            }
        }


        $this->comment("end");
    }


    public function fund($user_id)
    {
        $currency = Currency::where('is_micro', 1)->get();
        if (empty($currency)) {
            return 0;
        }
        $price = 0;
        foreach ($currency as $v) {
            $user_wallet = UsersWallet::where('user_id', $user_id)->where('currency', $v->id)->first();
            if (!empty($user_wallet)) {
                $fund = $user_wallet->micro_balance * $v->price;
                $price += $fund;
            }
        }
        return $price;
    }
}
