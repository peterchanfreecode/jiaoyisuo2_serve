<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\AgentReport;
use Illuminate\Console\Command;
use App\Models\Users;
use App\Models\ChargeReq;
use App\Models\UsersWalletOut;
use App\Models\Agent;
use App\Models\RebateFlow;
class Agent_report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '代理报表';

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
        $this->info('--------------------------------------------------');
        $this->info('开始执行代理报表:' . now()->toDateTimeString());

        Agent::where("status", 1)
            ->chunkById(100, function ($agents) use ($start_time,$end_time) {
                foreach ($agents as $agent) {
                    if ((time() - 300) < $start_time) {
                        $c_time = strtotime(date("Y-m-d", strtotime("-1 day")));
                        $e_time = strtotime(date("Y-m-d", strtotime("-1 day")) . "235959");
                        $this->calculation($c_time, $e_time,$agent);
                    }
                    $this->calculation($start_time, $end_time,$agent);
                    $this->info('开始执行代理'.$agent->id);
                }
            });
        $this->info('执行结束');
    }


    function calculation($start_time, $end_time,$agent)
    {
        $data = [];
        $this->install($data, $start_time, $end_time,$agent);
        $c_date = date("Y-m-d", $end_time);
        if ($data) {
            $info = AgentReport::where("date_time", $c_date)->where("agent_id",$agent->id)->first();
            if ($info) {
                $info->total_candy_number = $data["total_candy_number"] ?? 0.00;
                $info->user_num = $data["user_num"] ?? 0;
                $info->total_user_num = $data["total_user_num"] ?? 0;
                $info->charge_amount = $data["charge_amount"] ?? 0.00;
                $info->total_charge_amount = $data["total_charge_amount"] ?? 0.00;
                $info->withdraw_amount = $data["withdraw_amount"] ?? 0.00;
                $info->total_withdraw_amount = $data["total_withdraw_amount"] ?? 0.00;
                $info->rebate_amount = $data["rebate_amount"] ?? 0.00;
                $info->total_rebate_amount = $data["total_rebate_amount"] ?? 0.00;
                $info->save();
            } else {
                $model = new AgentReport();
                $model->date_time = $c_date;
                $model->agent_id = $agent->id;
                $model->total_candy_number = $data["total_candy_number"] ?? 0.00;
                $model->user_num = $data["user_num"] ?? 0;
                $model->total_user_num = $data["total_user_num"] ?? 0;
                $model->charge_amount = $data["charge_amount"] ?? 0.00;
                $model->total_charge_amount = $data["total_charge_amount"] ?? 0.00;
                $model->withdraw_amount = $data["withdraw_amount"] ?? 0.00;
                $model->total_withdraw_amount = $data["total_withdraw_amount"] ?? 0.00;
                $model->rebate_amount = $data["rebate_amount"] ?? 0.00;
                $model->total_rebate_amount = $data["total_rebate_amount"] ?? 0.00;
                $model->save();
            }
        }
    }

    function install(&$data, $start_time, $end_time,$agent)
    {
        //今日新增用户
        $users = new Users();
        $uids = $users->whereRaw("FIND_IN_SET($agent->id,agent_path)")->pluck("id")->all();
        if(!$uids){
            return false;
        }
        $total_candy_number = Users::wherein("id",$uids)->sum("candy_number");
        $data["total_candy_number"] = $total_candy_number;
        $user_count = Users::wherein("id",$uids)->where("time", ">=", $start_time)->
                where("time", "<", $end_time)->where("is_real_user", 1)->count();
        $data["user_num"] = $user_count;
        //总人数
        $total_user_count = Users::wherein("id",$uids)->where("is_real_user", 1)->count();
        $data["total_user_num"] = $total_user_count;
        //今日充值金额
        $data["charge_amount"] = 0;
        $s_time = date("Y-m-d H:i:s", $start_time);
        $e_time = date("Y-m-d H:i:s", $end_time);
        $list = ChargeReq::wherein("uid",$uids)->where("updated_at", ">=", $s_time)->where("updated_at", "<", $e_time)->where("status", 2)
            ->whereHas('user', function ($query) {
                $query->where('is_real_user', 1);
            })->get();
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
            $data["charge_amount"] = $total_money;
        }
        $data["total_charge_amount"] = 0;
        $list = ChargeReq::wherein("uid",$uids)->where("status", 2)
            ->whereHas('user', function ($query) {
                $query->where('is_real_user', 1);
            })->get();
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
            $data["total_charge_amount"] = $total_money;
        }
        //今日提现
        $data["withdraw_amount"] = 0;
        $list = UsersWalletOut::wherein("user_id",$uids)->where("update_time", ">=", $start_time)->where("update_time", "<", $end_time)->where("status", 2)
            ->whereHas('user', function ($query) {
                $query->where('is_real_user', 1);
            })->get();
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
        $data["total_withdraw_amount"] = 0;
        $list = UsersWalletOut::wherein("user_id",$uids)->where("status", 2)
            ->whereHas('user', function ($query) {
                $query->where('is_real_user', 1);
            })->get();
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
            $data["total_withdraw_amount"] = $total_money;
        }
        $data["rebate_amount"] = 0;
        $rebate_amount = RebateFlow::wherein("receive_user_id",$uids)
           ->where("create_time", ">=", $s_time)->where("create_time", "<", $e_time)
            ->whereHas('user', function ($query) {
                $query->where('is_real_user', 1);
            })->sum("rebate_amount");
        $data["rebate_amount"] = $rebate_amount;
        //总返佣
        $data["total_rebate_amount"] = 0;
        $rebate_amount = RebateFlow::wherein("receive_user_id",$uids)
            ->whereHas('user', function ($query) {
                $query->where('is_real_user', 1);
            })->sum("rebate_amount");
        $data["total_rebate_amount"] = $rebate_amount;
    }
}
