<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ChargeReq;
use App\Models\UsersWalletOut;
use App\Models\UserReal;
use App\Service\MainService;
use App\Models\UserRealHigh;
class MainController extends Controller{

    public function info(Request $request){
        $uid =  session()->get('admin_id');
        $arr_req = $arr_wallet = $arr_real = $arr_real2 = [];
        $req = ChargeReq::where("status",1)->orderBy('id','asc')->get();
        if($req){
            foreach($req as $v){
                $rel = MainService::main("charge_reg:".$uid,$v["id"]);
                if($rel){
                    $arr_req[] = [
                        "type"=>1,
                        "title"=>"你有一笔充值订单未处理",
                    ];
                }
            }
        }
        $wallet = UsersWalletOut::where("status",1)->orderBy('id','asc')->get();
        if($wallet){
            foreach($wallet as $v){
                $rel = MainService::main("wallet:".$uid,$v["id"]);
                if($rel){
                    $arr_wallet[] = [
                        "type"=>2,
                        "title"=>"你有一笔提现订单未处理",
                    ];
                }
            }
        }
        $real = UserReal::where("status",0)->orderBy('id','asc')->get();
        if($real){
            foreach($real as $v){
                $rel = MainService::main("real:".$uid,$v["id"]);
                if($rel){
                    $arr_real[] = [
                        "type"=> 3,
                        "title"=>"你有初级实名认证未处理",
                    ];
                }
            }
        }
        $real2 = UserRealHigh::where("status",0)->orderBy('id','asc')->get();
        if($real2){
            foreach($real2 as $v){
                $rel = MainService::main("real2:".$uid,$v["id"]);
                if($rel){
                    $arr_real2[] = [
                        "type"=> 4,
                        "title"=>"你有高级实名认证未处理",
                    ];
                }
            }
        }
        $list = array_merge($arr_req , $arr_wallet , $arr_real, $arr_real2);
        return response()->json(['code'=>0,'msg'=>'','data'=>$list]);
    }


}
?>