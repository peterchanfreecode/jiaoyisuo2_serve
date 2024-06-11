<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\DailyReport;
use Illuminate\Console\Command;
use App\Models\Users;
use App\Models\ChargeReq;
use App\Models\UsersWalletOut;

class Daily_report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '日报表';

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
        $start_time = strtotime(date("Ymd", time()));
        $end_time = strtotime(date("Ymd", time()) . "235959");
        if ((time() - 300) < $start_time) {
            $c_time = strtotime(date("Y-m-d", strtotime("-1 day")));
            $e_time = strtotime(date("Y-m-d", strtotime("-1 day")) . "235959");
            $this->calculation($c_time, $e_time);
        }
        $this->calculation($start_time, $end_time);

        /*        $info = ChargeReq::orderBy('id', 'asc')->first();
                $days = intval((time() - strtotime($info->created_at)) / 86400) + 10;
                $s_time = strtotime(date("Ymd", time()));
                $e_time = strtotime(date("Ymd", time()) . "235959");
                for ($i = $days; $i >= 0; $i--) {
                    $start_time = $s_time - ($i * 86400);
                    $end_time = $e_time - ($i * 86400);
                    $this->calculation($start_time, $end_time);
                }*/

    }


    function calculation($start_time, $end_time)
    {
        $data = [];
        $this->install($data, $start_time, $end_time);
        $c_date = date("Y-m-d", $end_time);

        if ($data) {
            $info = DailyReport::where("c_date", $c_date)->first();
            if ($info) {
                $info->install_count = $data["install_count"] ?? 0;
                $info->rep_amount = $data["rep_amount"] ?? 0;
                $info->withdraw_amount = $data["withdraw_amount"] ?? 0;
                $info->profit_amount = $data["profit_amount"] ?? 0;
                $info->month_profit_amount = $data["month_profit_amount"] ?? 0;
                $info->save();
            } else {
                $model = new DailyReport();
                $model->c_date = $c_date;
                $model->install_count = $data["install_count"] ?? 0;
                $model->rep_amount = $data["rep_amount"] ?? 0;
                $model->withdraw_amount = $data["withdraw_amount"] ?? 0;
                $model->profit_amount = $data["profit_amount"] ?? 0;
                $model->month_profit_amount = $data["month_profit_amount"] ?? 0;
                $model->save();
            }
        }
    }

    function install(&$data, $start_time, $end_time)
    {
        //今日新增用户
        $user_count = Users::where("time", ">=", $start_time)->where("time", "<", $end_time)->where("is_real_user", 1)->count();
        $data["install_count"] = $user_count;
        //今日充值金额
        $data["rep_amount"] = 0;
        $s_time = date("Y-m-d H:i:s", $start_time);
        $e_time = date("Y-m-d H:i:s", $end_time);
        $list = ChargeReq::where("updated_at", ">=", $s_time)->where("updated_at", "<", $e_time)->where("status", 2)->where("is_real",1)->get();
        if ($list) {
            $total_money = 0;
            foreach ($list as $v) {
                if ($v["currency_id"] !== 3) {
                    $return = Currency::where(['id' => $v['currency_id']])->first();
                    $now_price = $return["price"] ?? 1;
                    $total = $v["amount"] * $now_price;
                    $total_money += $total;
                } else {
                    $total_money += $v["amount"];
                }
            }
            $data["rep_amount"] = $total_money;
        }
        //今日提现
        $data["withdraw_amount"] = 0;
        $list = UsersWalletOut::where("create_time", ">=", $start_time)->where("create_time", "<", $end_time)->where("status", 2)->where("is_real",1)->get();
        if ($list) {

            $total_money = 0;
            foreach ($list as $v) {
                if ($v["currency"] !== 3) {
                    $return = Currency::where(['id' => $v['currency_id']])->first();
                    $now_price = $return["price"] ?? 1;
                    $total = $v["number"] * $now_price;
                    $total_money += $total;
                } else {
                    $total_money += $v["number"];
                }
            }
            $data["withdraw_amount"] = $total_money;
        }
        //今日盈利
        $data["profit_amount"] = $data["rep_amount"] - $data["withdraw_amount"];
        //月盈利
        $s_time = date("Y-m-01", $start_time);
        $e_time = date("Y-m-d H:i:s", $end_time);
        $list = ChargeReq::where("updated_at", ">=", $s_time)->where("updated_at", "<", $e_time)->where("status", 2)->where("is_real",1)->get();
        $month_req_money = 0;

        if ($list) {
            foreach ($list as $v) {
                if ($v["currency_id"] !== 3) {
                    $return = Currency::where(['id' => $v['currency_id']])->first();
                    $now_price = $return["price"] ?? 1;
                    $total = $v["amount"] * $now_price;
                    $month_req_money += $total;
                } else {
                    $month_req_money += $v["amount"];
                }
            }
        }

        $s_time = strtotime(date("Y-m-01", $start_time));
        $list = UsersWalletOut::where("create_time", ">=", $s_time)->where("create_time", "<", $end_time)->where("status", 2)->where("is_real",1)->get();
        $month_withdraw_money = 0;
        if ($list) {
            foreach ($list as $v) {
                if ($v["currency"] !== 3) {
                    $return = Currency::where(['id' => $v['currency_id']])->first();
                    $now_price = $return["price"] ?? 1;
                    $total = $v["number"] * $now_price;
                    $month_withdraw_money += $total;
                } else {
                    $month_withdraw_money += $v["number"];
                }
            }
        }
        $data["month_profit_amount"] = $month_req_money - $month_withdraw_money;
    }
}
