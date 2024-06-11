<?php

namespace App\Http\Controllers\Api;
use App\Models\AreaCode;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\MarketHour;
class CurrencyController extends Controller
{
    public function area_code()
    {
        $AreaCode = AreaCode::get()->toArray();
        return $this->success($AreaCode);
    }

    public function newQuotation()
    {
        $currency = Currency::with('quotation')
            ->whereHas('quotation', function ($query) {
                $query->where('is_display', 1);
            })
            ->where('is_display', 1)
            ->get();
        if($currency[0]["quotation"]){
            $arr = [];
            foreach($currency[0]["quotation"] as $k=>$v){
                if( $v->open_microtrade ==0){
                    unset($currency[0]["quotation"][$k]);
                }else{
                    $arr[] = $v;
                }

            }
            unset($currency[0]["quotation"]);
            $currency[0]["quotation"] = $arr;
        }
        return $this->success($currency);
    }
    public function klineMarket(Request $request)
    {
        $symbol = $request->input('symbol');
        $period = $request->input('period');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $symbol = strtoupper($symbol);
        $result = [];
        //类型，1=15分钟，2=1小时，3=4小时,4=一天,5=分时,6=5分钟，7=30分钟,8=一周，9=一月,10=一年
        $period_list = [
            '1min' => '1min',
            '5min' => '5min',
            '15min' => '15min',
            '30min' => '30min',
            '60min' => '60min',
            '1H' => '60min',
            '1D' => '1day',
            '1W' => '1week',
            '1M' => '1mon',
            '1Y' => '1year',
            '1day' => '1day',
            '1week' => '1week',
            '1mon' => '1mon',
            '1year' => '1year',
        ];
        if ($from == null || $to == null) {
            return [
                'code' => -1,
                'msg' => 'error: from time or to time must be filled in',
                'data' => $result,
            ];
        }
        if ($from > $to) {
            return [
                'code' => -1,
                'msg' => 'error: from time should not exceed the to time.',
                'data' => $result,
            ];
        }
        $periods = array_keys($period_list);
        if ($period == '' || !in_array($period, $periods)) {
            return [
                'code' => -1,
                'msg' => 'error: period invalid',
                'data' => $result,
            ];
        }
        if ($symbol == '' || stripos($symbol, '/') === false) {
            return [
                'code' => -1,
                'msg' => 'error: symbol invalid',
                'data' => $result,
            ];
        }
        $period = $period_list[$period];
        list($base_currency, $quote_currency) = explode('/', $symbol);
        $base_currency_model = Currency::where('name', $base_currency)
            ->where("is_display", 1)
            ->first();

        if (!$base_currency_model) {
            return [
                'code' => -1,
                'msg' => 'error: symbol not exist',
                'data' => null
            ];
        }
        $result = MarketHour::getEsearchMarket($base_currency, $quote_currency, $period, $from, $to);
        $result = array_map(function ($value) {
            $value['time'] = $value['id'] * 1000;
            $value['volume'] = $value['amount'] ?? 0;
            return $value;
        }, $result);
        return [
            'code' => 1,
            'msg' => 'success',
            'data' => $result
        ];
    }
}
