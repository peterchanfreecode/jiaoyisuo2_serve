<?php

namespace App\Console\Commands;

use App\Models\MicroOrder;
use App\Models\UserChat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendMicroOrder;

class Micro_send extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'micro_send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '推送秒合约时间';

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
        $this->info('开始推送');
        while (true) {
            $time = time()+1800;
            $order = MicroOrder::where("created_at", ">", $time)->get();
            if ($order) {
                foreach ($order as  $v) {
                    $c_time = time() - strtotime($v->created_at);
                    $this->info("推送秒合约订单:".$v->id);
                    if ($c_time < $v->seconds) {//执行推送
                        $data = [
                            "id"=>$v->id,
                            "user_id"=>$v->user_id,
                            "seconds"=>($v->seconds-$c_time),
                        ];

                    }else{
                        $data = [
                            "id"=>$v->id,
                            "user_id"=>$v->user_id,
                            "seconds"=>0,
                        ];
                    }
                    $send_data = [
                        'type' => 'micro_time',
                        'data' => $data,
                    ];
                    SendMicroOrder::dispatch($send_data)->onQueue('micro_send:handle');

                }
            }
            sleep(1);
        }

    }

}
