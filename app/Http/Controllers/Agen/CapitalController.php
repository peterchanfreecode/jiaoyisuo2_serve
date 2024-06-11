<?php

namespace App\Http\Controllers\Agen;
use App\Events\RechargeEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{AccountLog, Agent, ChargeReq,Setting,Users,UsersWalletOut,Currency,UsersWallet};
use Illuminate\Support\Facades\Validator;

class CapitalController extends Controller
{

    //充币
    public function rechargeIndex()
    {
        //币币
        $legal_currencies = Currency::get();
        //下级代理
        $son_agents = Agent::getAllChildAgent(Agent::getAgentId());
        return view("agen.capital.recharge", [
            'legal_currencies' => $legal_currencies,
            'son_agents' => $son_agents,
        ]);
    }

    //充币申请
    public function rechargeApply()
    {
        //币币
        $legal_currencies = Currency::get();
        //下级代理
        $son_agents = Agent::getAllChildAgent(Agent::getAgentId());
        $self = Agent::getAgent()->toArray();
        return view("agen.capital.rechargeapply", [
            'legal_currencies' => $legal_currencies,
            'son_agents' => $son_agents,
            "level" => $self["level"]
        ]);
    }

    //提币
    public function withdrawIndex()
    {
        //币币
        $legal_currencies = Currency::get();
        //下级代理
        $son_agents = Agent::getAllChildAgent(Agent::getAgentId());
        $self = Agent::getAgent()->toArray();
        return view("agen.capital.withdraw", [
            'legal_currencies' => $legal_currencies,
            'son_agents' => $son_agents,
            "level" => $self["level"]
        ]);
    }

    public function rechargeList(Request $request)
    {
        $limit = $request->input('limit', 20);
        $agent_id = Agent::getAgentId();
        $node_users = Users::whereRaw("FIND_IN_SET($agent_id,`agent_path`)")->pluck('id')->all();
        $lists = AccountLog::where('type', AccountLog::CHANGEREQ)
            ->whereIn('user_id', $node_users)
            ->where(function ($query) use ($request) {

                $account_number = $request->input('account_number', '');
                $belong_agent = $request->input('belong_agent', '');
                $currency_id = $request->input('currency_id', -1);
                $uid = $request->input('uid', '');
                $query->when($account_number != '', function ($query) use ($account_number) {
                    $query->whereHas('user', function ($query) use ($account_number) {
                        $query->where('account_number', $account_number);
                    });
                })->when($uid != '', function ($query) use ($uid) {
                    $query->whereHas('user', function ($query) use ($uid) {
                        $query->where('id', $uid);
                    });
                })->when($belong_agent != '', function ($query) use ($belong_agent) {
                    $query->whereHas('user', function ($query) use ($belong_agent) {
                        $query->whereHas('belongAgent', function ($query) use ($belong_agent) {
                            $query->where('username', $belong_agent);
                        });
                    });
                })->when($currency_id > 0, function ($query) use ($currency_id) {
                    $query->where('currency', $currency_id);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($limit);
        $items = $lists->getCollection();
        $items->transform(function ($item, $key) {

            $item->setAttribute('belong_agent_name', $item->user->belongAgent->username ?? '');
            return $item;
        });
        $lists->setCollection($items);

        return $this->layuiData($lists);
    }

    public function applyList(Request $request)
    {
        $limit = $request->input('limit', 20);

        $agent_id = Agent::getAgentId();
        $node_users = Users::whereRaw("FIND_IN_SET($agent_id,`agent_path`)")->pluck('id')->all();
        $lists = ChargeReq::whereIn('uid', $node_users)
            ->where(function ($query) use ($request) {

                $account_number = $request->input('account_number', '');
                $belong_agent = $request->input('belong_agent', '');
                $currency_id = $request->input('currency_id', -1);
                $status = $request->input('status', 0);
                $uid = $request->input('uid', -1);
                $query->when($account_number != '', function ($query) use ($account_number) {
                    $query->whereHas('user', function ($query) use ($account_number) {
                        $query->where('account_number', $account_number);
                    });
                })->when($belong_agent != '', function ($query) use ($belong_agent) {
                    $query->whereHas('user', function ($query) use ($belong_agent) {
                        $query->whereHas('belongAgent', function ($query) use ($belong_agent) {
                            $query->where('username', $belong_agent);
                        });
                    });
                })->when($currency_id > 0, function ($query) use ($currency_id) {
                    $query->where('currency_id', $currency_id);
                })->when($status > 0, function ($query) use ($status) {
                    $query->where('status', $status);
                })->when($uid > 0, function ($query) use ($uid) {
                    $query->where('uid', $uid);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($limit);

        $items = $lists->getCollection();
        $items->transform(function ($item, $key) {
            // 设置上级代理商信息
            $item->setAttribute('belong_agent_name', $item->user->belongAgent->username ?? '');
            return $item;
        });
        $lists->setCollection($items);
        $lists = ChargeReq::get_en_lists($lists);
        return $this->layuiData($lists);
    }


    //提币
    public function withdrawList(Request $request)
    {
        $limit = $request->input('limit', 20);
        $agent_id = Agent::getAgentId();
        $node_users = Users::whereRaw("FIND_IN_SET($agent_id,`agent_path`)")->pluck('id')->all();;
        $lists = UsersWalletOut::where('status', ">", 0)
            ->whereIn('user_id', $node_users)
            ->where(function ($query) use ($request) {

                $account_number = $request->input('account_number', '');
                $belong_agent = $request->input('belong_agent', '');
                $user_id = $request->input('user_id', "");
                $currency_id = $request->input('currency_id', -1);
                $query->when($account_number != '', function ($query) use ($account_number) {
                    $query->whereHas('user', function ($query) use ($account_number) {
                        $query->where('account_number', $account_number);
                    });
                })->when($belong_agent != '', function ($query) use ($belong_agent) {
                    $query->whereHas('user', function ($query) use ($belong_agent) {
                        $query->whereHas('belongAgent', function ($query) use ($belong_agent) {
                            $query->where('username', $belong_agent);
                        });
                    });
                })->when($currency_id > 0, function ($query) use ($currency_id) {
                    $query->where('currency', $currency_id);
                })->when($user_id > 0, function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($limit);

        $items = $lists->getCollection();
        $items->transform(function ($item, $key) {
            $item->setAttribute('belong_agent_name', $item->user->belongAgent->username ?? '');
            return $item;
        });
        $lists->setCollection($items);
        $lists = UsersWalletOut::get_en_lists($lists);
        return $this->layuiData($lists);
    }

    //用户资金
    public function wallet(Request $request)
    {
        $id = $request->get('id', null);
        if (empty($id)) {
            return $this->error('Parameter error');
        }
        $self = Agent::getAgent()->toArray();
        return view("agen.capital.wallet", ['user_id' => $id,"level" => $self["level"]]);
    }

    public function wallettotalList(Request $request)
    {
        $limit = $request->get('limit', 10);
        $user_id = $request->get('user_id', null);
        if (empty($user_id)) {
            return $this->error('Parameter error');
        }

        $list = UsersWallet::where("user_id",$user_id)->orderBy('id', 'asc')->paginate($limit);
        foreach ($list->items() as &$value) {
            $value->_ru = AccountLog::where('type', AccountLog::CHANGEREQ)
                ->where('user_id', $user_id)
                ->where('currency', $value->id)
                ->sum('value');

            $value->_chu = UsersWalletOut::where('status', 2)
                ->where('user_id', $user_id)
                ->where('currency', $value->currency)
                ->sum('real_number');
            $info = UsersWallet::where('user_id', $user_id)
                ->where('currency', $value->currency)
                ->first();
            $value->change_balance = $info->change_balance ?? 0;
            $value->lock_change_balance = $info->lock_change_balance ?? 0;
        }

        return $this->layuiData($list);
    }
    /*
       * 调节账户
       * */
    public function conf(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error('Parameter error');
        }
        $result = UsersWallet::find($id);
        if (empty($result)) {
            return $this->error('no such result');
        }
        $account = Users::where('id', $result->user_id)->value('phone');
        if (empty($account)) {
            $account = Users::where('id', $result->user_id)->value('email');
        }
        $result['account'] = $account;
        return view('agen.capital.conf', ['results' => $result]);
    }

    //调节账号  type  1法币交易余额  2法币交易锁定余额 3币币交易余额 4币币交易锁定余额  5杠杆交易余额 6杠杆交易锁定余额
    public function postConf(Request $request)
    {
        $message = [
            'required' => ':attribute 不能为空',
        ];
        $validator = Validator::make($request->all(), [
            'way' => 'required',   //增加 increment；减少 decrement
            'type' => 'required',       //原生余额1；消费余额2；增值余额3；可增加其他账户调节字段
            'conf_value' => 'required',       //值
        ], $message);

        //以上验证通过后 继续验证
        $validator->after(function ($validator) use ($request) {

            $wallet = UsersWallet::find($request->get('id'));
            if (empty($wallet)) {
                return $validator->errors()->add('isUser', 'no such wallet');
            }
            $user = Users::getById($wallet->user_id);
            if (empty($user)) {
                return $validator->errors()->add('isUser', 'no such user');
            }
            $way = $request->get('way', 'increment');
            $type = $request->get('type', 1);
            $conf_value = $request->get('conf_value', 0);
            if ($type == 3 && $way == 'decrement') {
                if ($wallet->change_balance < $conf_value) {
                    return $validator->errors()->add('isBalance', 'This wallet has insufficient currency transaction balance' . $conf_value);
                }
            }

        });
        //如果验证不通过
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $id = $request->get('id', null);
        $way = $request->get('way', 'increment');
        $type = $request->get('type', 1);
        $conf_value = $request->get('conf_value', 0);
        $info = $request->get('info', ':');
        $wallet = UsersWallet::find($id);
        $user = Users::getById($wallet->user_id);
        $data_wallet['wallet_id'] = $id;
        $data_wallet['create_time'] = time();
        DB::beginTransaction();
        try {
            if ($type == 3) {
                $data_wallet['balance_type'] = 2;
                $data_wallet['lock_type'] = 0;
                $data_wallet['before'] = $wallet->change_balance;
                if ($way == 'increment') {
                    $data_wallet['change'] = $conf_value;
                    $data_wallet['after'] = bc_add($wallet->change_balance, $conf_value, 5);
                    $wallet->increment('change_balance', $conf_value);
                    AccountLog::insertLog(['user_id' => $user->id, 'value' => $conf_value, 'info' => AccountLog::getTypeInfo(AccountLog::ADMIN_CHANGE_BALANCE) . ":" . $info, 'type' => AccountLog::ADMIN_CHANGE_BALANCE, 'currency' => $wallet->currency], $data_wallet);
                    try {
                        event(new RechargeEvent($wallet->user_id, 1));//充值成功增加3级内有效邀请人
                    } catch (\Exception $ex) {

                    }
                } else {
                    $data_wallet['change'] = $conf_value * -1;
                    $data_wallet['after'] = bc_sub($wallet->change_balance, $conf_value, 5);
                    $wallet->decrement('change_balance', $conf_value);
                    AccountLog::insertLog(['user_id' => $user->id, 'value' => $conf_value * -1, 'info' => AccountLog::getTypeInfo(AccountLog::ADMIN_CHANGE_BALANCE) . ":" . $info, 'type' => AccountLog::ADMIN_CHANGE_BALANCE, 'currency' => $wallet->currency], $data_wallet);
                }
            }
            DB::commit();
            return $this->success('Successful operation');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    //用户资金
    public function flow(Request $request)
    {
        $id = $request->get('id', null);
        if (empty($id)) {
            return $this->error('Parameter error');
        }
        //获取type类型
        $type = array(
            AccountLog::CHANGEREQ => 'recharge',
            AccountLog::ADMIN_CHANGE_BALANCE => 'Manual recharge',
            AccountLog::WALLETOUT => 'withdraw',
            AccountLog::TRANSACTIONIN => 'Currency trading',
            AccountLog::TRANSACTION_FEE => 'Currency transaction fee',
            AccountLog::MICRO_TRADE => 'second contract',
            AccountLog::MICRO_TRADE_FREE => 'Second contract fee',
            AccountLog::LH_LOAN => 'pledge',
            AccountLog::IEO_BUY => 'purchase',
            AccountLog::IEO_REFUSE => 'Purchase rejected',
            AccountLog::IEO_PASS => 'Subscription passed',
            AccountLog::IEO_UN_THAW => 'Subscription Unfreeze',
            AccountLog::DEBIT_BALANCE_MINUS => 'urrency exchange',
        );
        $currency_type = Currency::all();
        return view("agen.capital.flow", [
            'types' => $type,
            'currency_type' => $currency_type,
            'user_id' => $id
        ]);
    }

    public function flow_lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $currency = $request->get('currency_type', 0);
        $type = $request->get('type', 0);
        $sign = $request->get('sign', 0);//正负号，0所有，1，正，-1，负号
        $user_id = $request->get('user_id', 0);
        $list = new AccountLog();
        $list = $list->where('user_id', $user_id);
        if (!empty($currency)) {
            $list = $list->where('currency', $currency);
        }
        if (!empty($type)) {
            $list = $list->where('type', $type);
        }
        if (!empty($sign)) {
            if ($sign > 0) {
                $list = $list->where('value', '>', 0);
            } else {
                $list = $list->where('value', '<', 0);
            }
        }
        $list = $list->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($list);
    }

    public function withdraw_show(Request $request)
    {
        $id = $request->get('id', '');
        if (!$id) {
            return $this->error('Parameter error');
        }
        $walletout = UsersWalletOut::find($id);
        $use_chain_api = Setting::getValueByKey('use_chain_api', 0);
        return view('agen.capital.withdraw_show', ['wallet_out' => $walletout, 'use_chain_api' => $use_chain_api,]);
    }

    public function withdraw_done(Request $request)
    {
        $id = $request->get('id', '');
        $method = $request->get('method', '');
        $notes = $request->get('notes', '');

        if (!$id) {
            return $this->error('Parameter error');
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
                $wallet_out->update_time = time();
                $wallet_out->save();
                $change_result = change_wallet_balance($user_wallet, 2, -$number, AccountLog::WALLETOUT, '提币成功,冻结余额减少', true);
                if ($change_result !== true) {
                    throw new \Exception($change_result);
                }
            } else {
                $wallet_out->status = 3;//提币失败状态
                $wallet_out->notes = $notes;//反馈的信息
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
            return $this->success('Successful operation');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

    public function passReq(Request $request)
    {
        $id = $request->get('id', 0);
        $is_real = $request->get('is_real', 0);

        if (empty($id)) {
            return $this->error('Parameter error');
        }
        if (empty($is_real)) {
            return $this->error('Please select the deposit typ');
        }
        $req = Db::table('charge_req')->where(['id' => $id, 'status' => 1])->first();
        if (!$req) {
            return $this->error('Recharge record error');
        }
        DB::table('charge_req')->where('id', $id)->update(['status' => 2, "is_real" => $is_real, 'updated_at' => date('Y-m-d H:i:s')]);
        $wallet = UsersWallet::where(['currency' => $req->currency_id, 'user_id' => $req->uid])->first();
        change_wallet_balance($wallet, 2, $req->amount, AccountLog::CHANGEREQ, 'Successful recharge to increase the balance');
        try {
            event(new RechargeEvent($req->uid, 1));//充值成功增加3级内有效邀请人
        } catch (\Exception $ex) {
            return $this->error('Recharge failed');
        }
        return $this->success('Recharge successful');
    }

    public function req_show(Request $request)
    {
        $id = $request->get('id', '');
        if (!$id) {
            return $this->error('Parameter error');
        }
        return view('agen.capital.apply_show', ["id" => $id]);
    }

    public function refuseReq(Request $request)
    {
        $id = $request->get('id', 0);
        $remark = $request->get('remark', "");
        if (empty($id)) {
            return $this->error('Parameter error');
        }
        if (empty($remark)) {
            return $this->error('Please enter a reason for rejection');
        }
        $req = Db::table('charge_req')->where(['id' => $id, 'status' => 1])->first();
        if (!$req) {
            return $this->error('Recharge record error');
        }

        DB::table('charge_req')->where('id', $id)->update(['status' => 3, 'remark' => $remark]);
        return $this->success('refusal to succeed');
    }

}
