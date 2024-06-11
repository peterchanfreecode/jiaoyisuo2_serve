<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Users;
use Illuminate\Support\Facades\Redis;

class User_ip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user_ip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '查询真实ip';

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
        $this->info('开始查询');
        while (true) {
            $user_id = 1;
            if (Redis::GET("user_id")) {
                $user_id = Redis::GET("user_id");
            }
            $list = Users::where("id", ">",$user_id)->limit(5)->get();
            if ($list) {
                foreach ($list as $v) {
                    $ip = $v->last_login_ip;
                    $time = time();
                    if ($ip) {
                        $res = file_get_contents("https://www.ip125.com/api/{$ip}?lang=zh-CN&_={$time}");
                        Log::info($v->id."ip地址解析成功");
                        $res = json_decode($res, true);
                        if ($res) {
                            $countryCode = $res["countryCode"] ?? '';
                            if ($countryCode) {
                                if (!in_array($countryCode, ["HK", "KH", "CN", "PH", "TH", "MM"])) {
                                    Redis::HSET("user_check_ip", $v->id, $v->id);
                                    $v->is_real_user = 1;
                                }
                            }
                        }
                    }
                    Redis::SET("user_id", $v->id);
                    sleep(10);
                }

            }
            sleep(600);
        }

    }

}
