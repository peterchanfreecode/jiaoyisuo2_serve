<?php

namespace App\Http\Controllers\Admin;
use App\Models\CurrencyMatch;
use App\Models\MarketHour;
use App\Models\MyQuotation as Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class MyQuotation extends Controller
{
    //
    public function index()
    {
        $data = [
            'currencys' =>  CurrencyMatch::where('market_from', 3)->get()
        ];
        return view('admin.needle.quotation', [
            'data' => $data,
        ]);
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $result = new Quotation();
        $result = $result->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }


    public function reset(Request $request)
    {
         Quotation::where("id",">",0)->delete();
        $list =  Redis::keys('market:quotation:*');
        if($list){
            foreach($list as $val){
                 Redis::del($val);
            }
        }
        return ['code' => 1];
    }

}
