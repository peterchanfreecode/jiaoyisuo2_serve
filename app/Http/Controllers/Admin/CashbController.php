<?php

/**
 * 提币控制器
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\UsersWalletOut;
use App\Models\UsersWallet;
use App\Models\AccountLog;
use App\Models\Setting;
use App\Traits\CheckGoogleSecurityCodeTraint;
use Maatwebsite\Excel\Facades\Excel;

class CashbController extends Controller
{
    use CheckGoogleSecurityCodeTraint;

    public function index()
    {
        return view('admin.cashb.index');
    }

    public function cashbList(Request $request)
    {
        $limit = $request->get('limit', 20);
        $account_number = $request->input('account_number', '');
        $user_id = $request->input('user_id', '');
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $is_real = $request->input('is_real', '');
        $userWalletOut = new UsersWalletOut();
        $userWalletOutList = $userWalletOut->whereHas('user', function ($query) use ($account_number) {
            if ($account_number != '') {
                $query->where('account_number', $account_number);
            }
        })->when($user_id != '', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->when($start_time != '', function ($query) use ($start_time) {
            $query->where('create_time', '>=', strtotime($start_time));
        })->when($end_time != '', function ($query) use ($end_time) {
            $query->where('create_time', '<=', strtotime($end_time));
        })->when($is_real != '', function ($query) use ($is_real) {
            $query->where('is_real', $is_real);
        })->orderBy('id', 'desc')->paginate($limit);
        $items = $userWalletOutList->getCollection();
        $items->transform(function ($item, $key) {
            // 设置上级代理商信息
            $item->setAttribute('belong_agent_name', $item->user->belongAgent->username ?? '---');
            return $item;
        });
        $userWalletOutList->setCollection($items);
        return $this->layuiData($userWalletOutList);
    }

    public function show(Request $request)
    {
        $id = $request->get('id', '');
        if (!$id) {
            return $this->error('参数小错误');
        }
        $walletout = UsersWalletOut::find($id);
        $use_chain_api = Setting::getValueByKey('use_chain_api', 0);
        return view('admin.cashb.edit', ['wallet_out' => $walletout, 'use_chain_api' => $use_chain_api,]);

    }

    //test
    public function done(Request $request)
    {
        set_time_limit(0);
        $id = $request->get('id', '');
        $method = $request->get('method', '');
        $notes = $request->get('notes', '');
        $verificationcode = $request->input('verificationcode', '');
        $txid = $request->input('txid', '');
        if (!$id) {
            return $this->error('参数错误');
        }

        try {
            DB::beginTransaction();
            $wallet_out = UsersWalletOut::where('status', '<=', 1)->lockForUpdate()->findOrFail($id);
            $number = $wallet_out->number;
            $user_id = $wallet_out->user_id;
            $currency = $wallet_out->currency;
            $user_wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency)->lockForUpdate()->first();

            if ($method == 'done') {//确认提币
                $wallet_out->status = 2;//提币成功状态
                $wallet_out->notes = $notes;//反馈的信息
                $wallet_out->verificationcode = $verificationcode;
                $wallet_out->update_time = time();
                $is_real = $request->input('is_real', '');
                if (!$is_real) {
                    return $this->error('请选择提币类型');
                }
                $wallet_out->is_real = $is_real;
                $wallet_out->save();
                $change_result = change_wallet_balance($user_wallet, 2, -$number, AccountLog::WALLETOUT, '提币成功,冻结余额减少', true);
                if ($change_result !== true) {
                    throw new \Exception($change_result);
                }
            } else {
                $wallet_out->status = 3;//提币失败状态
                $wallet_out->notes = $notes;//反馈的信息
                $wallet_out->verificationcode = $verificationcode;
                $wallet_out->update_time = time();

                $wallet_out->save();
                $change_result = change_wallet_balance($user_wallet, 2, -$number, AccountLog::WALLETOUT, '提币失败,冻结余额减少', true);
                if ($change_result !== true) {
                    throw new \Exception($change_result);
                }
                $change_result = change_wallet_balance($user_wallet, 2, $number, AccountLog::WALLETOUT, '提币失败,余额撤回');
                if ($change_result !== true) {
                    throw new \Exception($change_result);
                }
            }
            DB::commit();
            return $this->success('操作成功:)');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

    //重置提币订单状态
    public function back(Request $request)
    {
        $id = $request->input('id', '');
        $stepcode = $request->input('stepcode', 0);
        if (!$id) {
            return $this->error('参数错误');
        }
        $key = Redis::get("google_key");
        if (!$key) {
            return $this->error('安全码未设置');
        }
        if ($this->checkSecurityCode($stepcode, $key) == false) {
            return $this->error('验证安全码失败');
        }
        try {
            DB::beginTransaction();
            $wallet_out = UsersWalletOut::find($id);
            $number = $wallet_out->number;
            $user_id = $wallet_out->user_id;
            $currency = $wallet_out->currency;
            $user_wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency)->lockForUpdate()->first();
            $wallet_out->status = 1;//提币成功状态
            $wallet_out->notes = "";//反馈的信息
            $wallet_out->update_time = time();
            $wallet_out->save();
            change_wallet_balance($user_wallet, 2, -$number, AccountLog::WALLETOUT, '重置提币扣除余额');
            change_wallet_balance($user_wallet, 2, $number, AccountLog::WALLETOUT, '重置提币冻结余额', true);
            DB::commit();
            return $this->success('操作成功:)');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

    //导出用户列表至excel
    public function csv(Request $request)
    {
        $account_number = $request->input('account_number', '');
        $user_id = $request->input('user_id', '');
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $is_real = $request->input('is_real', '');
        $userWalletOut = new UsersWalletOut();
        $list = $userWalletOut->whereHas('user', function ($query) use ($account_number) {
            if ($account_number != '') {
                $query->where('account_number', $account_number);
            }
        })->when($user_id != '', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->when($start_time != '', function ($query) use ($start_time) {
            $query->where('create_time', '>=', strtotime($start_time));
        })->when($end_time != '', function ($query) use ($end_time) {
            $query->where('create_time', '<=', strtotime($end_time));
        })->when($is_real != '', function ($query) use ($is_real) {
            $query->where('is_real', $is_real);
        })->orderBy('id', 'desc')->get()->toArray();
        $data = $list;

        return Excel::create('提币数据', function ($excel) use ($data) {
            $excel->sheet('提币数据', function ($sheet) use ($data) {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('提币ID');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('用户ID');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('用户名');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('虚拟币');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('数量');
                });
                $sheet->cell('F1', function ($cell) {
                    $cell->setValue('交易状态');
                });
                $sheet->cell('G1', function ($cell) {
                    $cell->setValue('提币时间');
                });
                $sheet->cell('H1', function ($cell) {
                    $cell->setValue('提币类型');
                });
                $sheet->cell('I1', function ($cell) {
                    $cell->setValue('手续费');
                });
                $sheet->cell('J1', function ($cell) {
                    $cell->setValue('实际提币');
                });
                $sheet->cell('k1', function ($cell) {
                    $cell->setValue('体验金扣除');
                });
                $sheet->cell('L1', function ($cell) {
                    $cell->setValue('备注');
                });
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        if ($value["status"] == 1) {
                            $status_name = "申请提币";
                        } else if ($value["status"] == 2) {
                            $status_name = "提币完成";
                        } else {
                            $status_name = "申请失败";
                        }

                        $i = $key + 2;
                        $sheet->cell('A' . $i, $value['id']);
                        $sheet->cell('B' . $i, $value['user_id']);
                        $sheet->cell('C' . $i, $value['account_number']);
                        $sheet->cell('D' . $i, $value['currency_name']);
                        $sheet->cell('E' . $i, $value['number']);
                        $sheet->cell('F' . $i, $status_name);
                        $sheet->cell('G' . $i, $value['create_time']);
                        $sheet->cell('H' . $i, $value['real_type']);
                        $sheet->cell('I' . $i, $value['rate']);
                        $sheet->cell('J' . $i, $value['real_number']);
                        $sheet->cell('K' . $i, $value['gold']);
                        $sheet->cell('L' . $i, $value['notes']);
                    }
                }
            });
        })->download('xlsx');
    }

    public function edit_adress(Request $request)
    {
        $id = $request->input('id', '');
        $adress = $request->input('adress', "");
        if (!$id) {
            return $this->error('参数错误');
        }
        if (!$adress) {
            return $this->error('参数错误');
        }
        $wallet_out = UsersWalletOut::find($id);
        $wallet_out->address = $adress;
        if ($wallet_out->save()) {
            return $this->success('操作成功');
        } else {
            return $this->error('操作失败');
        }


    }
}
