<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    public function post(Request $request)
    {

        $currency_name = $request->get('currency', '');
        $jingdu = $request->get('jingdu', 0);
        $end = $request->get('end_price', 0);

        $rate2 = $request->get('rate2', 0);
        $rate1 = $request->get('rate1', 0);
        $dates = $request->get('dates', 0);
        $currency_name = strtolower($currency_name . 'usdt');
        $key = "market.{$currency_name}.kline.1min";
        $t = json_decode(Redis::get($key), true);
        $rand_start = $t['tick']["close"]??0;
        $numbers = $this->getNumberArray($rand_start, 60, $end, $jingdu, $rate2, $rate1);

        $obj = [];
        $start = $rand_start;

        foreach ($numbers as $number) {

            $arr = ['open' => count($obj) == 0 ? $start : $obj[count($obj) - 1]['close']];
            $arr['close'] = $number;
            if (count($obj) == 0) {
                $arr['close'] = $number + ((rand(-1, 1)) * $_GET['fudu']);
            }
            $val = max(array_values($arr));
            $minVal = min(array_values($arr));

            $arr['high'] = $val + $_GET['fudu'];
            $arr['low'] = abs($minVal - $_GET['fudu']);

            array_walk($arr, function (&$val) {
                $val = sprintf('%.6f', $val);
            });
            $obj[] = $arr;

        }
        $rsp = [];
        $i = 0;
        foreach ($obj as $v) {
            $i++;
            $rsp[] = [date('Y-m-d H:i:s', strtotime("-{$i} minutes")), $v['open'], $v['high'], $v['low'], $v['close'], rand(0, 100)];
        }
        echo json_encode(['data' => $rsp, 'next' => date('Y-m-d', strtotime('+1 days', strtotime($dates)))]);
        die;
    }

    public function getNumberArray($start, $numbers, $end, $jingdu, $rate2, $rate1)
    {
        $now = [];

        for ($i = 0; $i < $numbers; $i++) {

            if ($i == 0) {
                $now[] = $start;
            } elseif ($i == $numbers - 1) {
                $now[] = $end;
            } else {
                $rand = $jingdu;
                if ($this->get_rand([$rate2, $rate1]) == 1) {
                    $now[] = $now[$i - 1] + $rand;
                } else {
                    $val = $now[$i - 1] - $rand;
                    if ($val < 0) {
                        $now[] = $jingdu;
                    } else {
                        $now[] = $now[$i - 1] - $rand;
                    }

                }
            }
        }
        return $now;
    }

    public function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }


}
