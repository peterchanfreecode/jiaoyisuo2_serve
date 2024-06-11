<?php

namespace App\Http\Controllers\Admin;

use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\Currency;

class DepositController extends Controller
{
    public function index()
    {
        return view('admin.deposit.index');
    }

    public function add(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            $result = new Deposit();
        } else {
            $result = Deposit::find($id);
        }
        $currency = Currency::where("id",3)->get();
        return view('admin.deposit.add')->with('result', $result)->with('currency', $currency);
    }

    public function postAdd(Request $request)
    {
        $id = $request->get('id', 0);
        $currency_id = $request->get('currency_id', '');
        $day = $request->get('day', '');
        $rate = $request->get('rate', '');
        $save_min = $request->get('save_min', '');
        $cancel_rate = $request->get('cancel_rate', '');
        $levelZn = $request->get('level_zn', '');
        $levelEn = $request->get('level_en', '');
        $levelJp = $request->get('level_jp', '');
        $levelKr = $request->get('level_kr', '');
        $levelDe = $request->get('level_de', '');
        $levelFra = $request->get('level_fra', '');
        $levelTh = $request->get('level_th', '');
        $levelVi = $request->get('level_vi', '');
        $levelHk = $request->get('level_hk', '');
        $levelPt = $request->get('level_pt', '');
        if (empty($id)) {
            $result = new Deposit();
        } else {
            $result = Deposit::find($id);
            if ($result == null) {
                return redirect()->back();
            }
        }
        $result->currency_id = $currency_id;
        $result->day = $day;
        $result->rate = $rate;
        $result->save_min = $save_min;
        $result->cancel_rate = $cancel_rate;
        $result->level_zh = $levelZn;
        $result->level_en = $levelEn;
        $result->level_jp = $levelJp;
        $result->level_kr = $levelKr;
        $result->level_de = $levelDe;
        $result->level_fra = $levelFra;
        $result->level_th = $levelTh;
        $result->level_vi = $levelVi;
        $result->level_hk = $levelHk;
        $result->level_pt = $levelPt;

        try {
            $result->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $result = new Deposit();
        $result = $result->leftJoin('currency', 'currency.id', '=', 'deposit.currency_id')
            ->select('deposit.*', 'currency.name as currency_name')->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }

    public function del(Request $request)
    {
        $id = $request->get('id', 0);
        $result = Deposit::find($id);
        if (empty($result)) {
            return $this->error('参数错误');
        }
        try {
            $result->delete();
            return $this->success('删除成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

}
