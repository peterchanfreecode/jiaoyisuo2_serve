<?php

namespace App\Http\Controllers\Admin;


use App\Events\RechargeEvent;
use App\Models\CurrencyMatch;
use App\Models\MyQuotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\DAO\UserDAO;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\{AccountLog, Agent, ChargeReq, CurrencyQuotation, Token, Users, UserCashInfo, UserReal, UsersWallet};
use Illuminate\Support\Facades\Redis;
use App\Models\UserGold;
use App\Service\RedisQuotation;
use App\Models\UserProfile;
use Maatwebsite\Excel\Facades\Excel;
class UserController extends Controller
{
    public function index()
    {
        return view("admin.user.index");
    }

    //导出用户列表至excel
    public function csv(Request $request)
    {
        $account = $request->get('account', '');
        $list = new Users();
        $list = $list->leftjoin("user_real", "users.id", "=", "user_real.user_id");
        if (!empty($account)) {
            $list = $list->where("phone", 'like', '%' . $account . '%')
                ->orwhere('email', 'like', '%' . $account . '%')
                ->orWhere('account_number', 'like', '%' . $account . '%');
        }
        $list = $list->select("users.*", "user_real.card_id")->orderBy('users.id', 'desc')->get();
        $data = $list;

        return Excel::create('用户数据', function ($excel) use ($data) {
            $excel->sheet('用户数据', function ($sheet) use ($data) {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('ID');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('账户名');
                });
                $sheet->cell('F1', function ($cell) {
                    $cell->setValue('邀请码');
                });
                $sheet->cell('G1', function ($cell) {
                    $cell->setValue('用户状态');
                });
                $sheet->cell('H1', function ($cell) {
                    $cell->setValue('头像');
                });
                $sheet->cell('I1', function ($cell) {
                    $cell->setValue('注册时间');
                });
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $i = $key + 2;
                        $sheet->cell('A' . $i, $value['id']);
                        $sheet->cell('B' . $i, $value['account_number']);
                        $sheet->cell('C' . $i, $value['top_upnumber']);
                        $sheet->cell('D' . $i, $value['zhitui_real_number']);
                        $sheet->cell('E' . $i, $value['real_teamnumber']);
                        $sheet->cell('F' . $i, $value['extension_code']);
                        $sheet->cell('G' . $i, $value['status']);
                        $sheet->cell('H' . $i, $value['head_portrait']);
                        $sheet->cell('I' . $i, $value['time']);
                    }
                }
            });
        })->download('xlsx');
    }

    //用户列表
    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        $name = $request->get('name', '');
        $risk = $request->get('risk', -2);
        $id = $request->get('id', '');

        $list = new Users();
        if (!empty($account)) {
            $list = $list->where('account_number', 'like', '%' . $account . '%');
        }
        if ($id) {
            $list = $list->where("id", $id);
        }
        $list = $list->when($name != '', function ($query) use ($name) {
            $query->whereHas('userReal', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            });
        });
        if ($risk != -2) {
            $list = $list->where('risk', $risk);
        }
        $list = $list->orderBy('id', 'desc')
            ->paginate($limit);

        $items = $list->getCollection();
        $items->transform(function ($item, $key) {
            return $item->append('risk_name');
        });
        $list->setCollection($items);
        $list = Users::get_admin_lists($list);
        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);
    }

    public function edit(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error("参数错误");
        }

        $result = new Users();
        $result = $result->leftjoin("user_real", "users.id", "=", "user_real.user_id")->select("users.*", "user_real.card_id")->find($id);
        $res = UserCashInfo::where('user_id', $id)->first();
        return view('admin.user.edit', ['result' => $result, 'res' => $res]);
    }

    //修改盘口
    public function changepan(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error("参数错误");
        }
        $result = DB::table('users')->find($id);
        return view('admin.user.changepan', ['result' => $result]);
    }

    public function dochangepan(Request $request)
    {
        $id = $request->post('id', 0);
        $pan_type = $request->post('pan_type', 0);
        if (empty($id || empty($pan_type))) {
            return $this->error("参数错误");
        }
        $r = DB::table('users')->where('id', $id)->update(['pan_type' => $pan_type]);
        if ($r) {
            return $this->success('编辑成功');
        }else{
            return $this->error('编辑失败');
        }
    }

    public function dochangepanmore(Request $request)
    {
        $ids = $request->post('ids', 0);
        $pan_type = $request->post('pan_type', 0);
        if (empty($ids || empty($pan_type))) {
            return $this->error("参数错误");
        }
        $r = DB::table('users')->whereIn('id', explode(',', $ids))->update(['pan_type' => $pan_type]);
        if ($r) {
            return $this->success('编辑成功');
        }else{
            return $this->error('编辑失败');
        }
    }

    //编辑用户信息
    public function doedit()
    {
        $card_id = Input::get("card_id");
        $password = Input::get("password");
        $risk = Input::get('risk', 0);

        $id = Input::get("id");
        if (empty($id)) return $this->error("参数错误");

        $user = Users::find($id);
        if (empty($user)) {
            return $this->error("数据未找到");
        }
        if (!empty($password)) {
            $user->password = Users::MakePassword($password);
        }
        $user->risk = $risk;
        DB::beginTransaction();

        try {
            $user->save();
            //更改身份证号
            if (!empty($card_id)) {
                $real = UserReal::where("user_id", "=", $id)->first();
                $real->card_id = $card_id;
                $real->save();
            }
            DB::commit();
            return $this->success('编辑成功');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

    public function lockUser(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error("参数错误");
        }
        $result = Users::find($id);
        //
        // $res=UserCashInfo::where('user_id',$id)->first();
        return view('admin.user.lock', ['result' => $result]);
    }

    public function doLock(Request $request)
    {
        $id = $request->get('id', 0);
        $date = $request->get('date', 0);
        $status = $request->get('status', 0);

        if (empty($id)) {
            return $this->error('参数错误');
        }
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if (empty($date)) {
            return $this->error('缺少时间！');
        }
        $users = new Users();
        $result = $users->lockUser($user, $status, $date);
        if (!$result) {
            return $this->error('锁定失败');
        }
        Token::deleteTokenByUserId($id);
        return $this->success('操作成功');
    }

    public function del(Request $request)
    {
        return $this->error('禁止删除用户,将会造成系统崩溃');
        $id = $request->get('id');
        $user = Users::getById($id);
        if (empty($user)) {
            $this->error("用户未找到");
        }
        try {
            $user->delete();
            return $this->success('删除成功');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    public function lock(Request $request)
    {
        $id = $request->get('id', 0);

        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if ($user->status == 1) {
            $user->status = 0;
        } else {
            $user->status = 1;
        }
        try {
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function bind(Request $request)
    {
        $id = $request->get('id', 0);
        $bind_id = $request->get('bind_id', 0);
        $check_bind = $request->get('check_bind', "");
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if (!$check_bind) {
            if ($user->parent_id) {
                return $this->error('此用户已绑定邀请人');
            }
        }
        if (!$bind_id) {
            return $this->error('请输入邀请人ID');
        }
        $p = Users::where("id", $bind_id)->first();
        if (!$p) {
            return $this->error('请输入邀请人不存在');
        }
        $parent_id = $p->id;
        $user->parent_id = $parent_id;
        try {
            $user->parents_path = UserDAO::getRealParentsPath($user); // 生成parents_path tian add

            // 代理商节点id。标注该用户的上级代理商节点。这里存的代理商id是agent代理商表中的主键，并不是users表中的id。
            $agent_note_id = Agent::reg_get_agent_id_by_parentid($parent_id);
            $user->agent_note_id = $agent_note_id;
            // 代理商节点关系
            $user->agent_path = Agent::agentPath($agent_note_id);
            $user->save(); // 保存到user表中
            $user->extension_code = Users::getExtensionCode();
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function bind_email(Request $request)
    {
        $id = $request->get('id', 0);
        $bind_id = $request->get('bind_id', 0);
        $check_bind = $request->get('check_bind', "");
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if (!$check_bind) {
            if ($user->parent_id) {
                return $this->error('此用户已绑定邀请人');
            }
        }
        if (!$bind_id) {
            return $this->error('请输入邀请人邮箱');
        }
        $p = Users::where("account_number", $bind_id)->first();
        if (!$p) {
            return $this->error('请输入邀请人不存在');
        }
        $parent_id = $p->id;
        $user->parent_id = $parent_id;
        try {
            $user->parents_path = UserDAO::getRealParentsPath($user); // 生成parents_path tian add

            // 代理商节点id。标注该用户的上级代理商节点。这里存的代理商id是agent代理商表中的主键，并不是users表中的id。
            $agent_note_id = Agent::reg_get_agent_id_by_parentid($parent_id);
            $user->agent_note_id = $agent_note_id;
            // 代理商节点关系
            $user->agent_path = Agent::agentPath($agent_note_id);
            $user->save(); // 保存到user表中
            $user->extension_code = Users::getExtensionCode();
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function wallet(Request $request)
    {
        $id = $request->get('id', null);
        if (empty($id)) {
            return $this->error('参数错误');
        }
        return view("admin.user.user_wallet", ['user_id' => $id]);
    }

    public function walletList(Request $request)
    {
        $limit = $request->get('limit', 10);
        $user_id = $request->get('user_id', null);
        if (empty($user_id)) {
            return $this->error('参数错误');
        }
        $list = new UsersWallet();
        $list = $list->where('user_id', $user_id)->orderBy('id', 'asc')->paginate($limit);

        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);
    }

//钱包锁定状态
    public function walletLock(Request $request)
    {
        $id = $request->get('id', 0);

        $wallet = UsersWallet::find($id);
        if (empty($wallet)) {
            return $this->error('参数错误');
        }
        if ($wallet->status == 1) {
            $wallet->status = 0;
        } else {
            $wallet->status = 1;
        }
        try {
            $wallet->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    /*
     * 调节账户
     * */
    public function conf(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error('参数错误');
        }
        $result = UsersWallet::find($id);
        if (empty($result)) {
            return $this->error('无此结果');
        }
        $account = Users::where('id', $result->user_id)->value('phone');
        if (empty($account)) {
            $account = Users::where('id', $result->user_id)->value('email');
        }
        $result['account'] = $account;
        return view('admin.user.conf', ['results' => $result]);
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
                return $validator->errors()->add('isUser', '没有此钱包');
            }
            $user = Users::getById($wallet->user_id);
            if (empty($user)) {
                return $validator->errors()->add('isUser', '没有此用户');
            }
            $way = $request->get('way', 'increment');
            $type = $request->get('type', 1);
            $conf_value = $request->get('conf_value', 0);
            if ($type == 3 && $way == 'decrement') {
                if ($wallet->change_balance < $conf_value) {
                    return $validator->errors()->add('isBalance', '此钱包币币交易余额不足' . $conf_value . '元');
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
            return $this->success('操作成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    //删除钱包
    public function delw(Request $request)
    {
        $id = $request->get('id');
        $wallet = UsersWallet::find($id);
        if (empty($wallet)) {
            $this->error("钱包未找到");
        }
        try {
            $wallet->delete();
            return $this->success('删除成功');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }


    //加入黑名单
    public function blacklist(Request $request)
    {
        $id = $request->get('id', 0);

        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if ($user->is_blacklist == 1) {
            $user->is_blacklist = 0;
        } else {
            $user->is_blacklist = 1;
        }
        try {
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function batchRisk(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $risk = $request->input('risk', 0);
            if (empty($ids)) {
                throw new \Exception('请先选择用户');
            }
            if (!in_array($risk, [-1, 0, 1])) {
                throw new \Exception('风控类型不正确');
            }
            $affect_rows = Users::whereIn('id', $ids)
                ->update([
                    'risk' => $risk,
                ]);
            return $this->success('本次提交:' . count($ids) . '条,设置成功:' . $affect_rows . '条');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function chargeList(Request $request)
    {
        $limit = $request->get('limit', 20);
        $account = $request->input('account_number', '');
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $uid = $request->input('uid', '');
        $is_real = $request->input('is_real', '');

        $list = ChargeReq::join('users', 'users.id', '=', 'charge_req.uid')
            ->join('currency', 'currency.id', '=', 'charge_req.currency_id')
            ->select('charge_req.*', 'users.account_number', 'currency.name')
            ->when($account != '', function ($query) use ($account) {
                $query->where("charge_req.user_account", 'like', '%' . $account . '%');
            })->when($uid != '', function ($query) use ($uid) {
                $query->where('charge_req.uid', $uid);
            })->when($start_time != '', function ($query) use ($start_time) {
                $query->where('charge_req.created_at', '>=', $start_time);
            })->when($end_time != '', function ($query) use ($end_time) {
                $query->where('charge_req.created_at', '<=', $end_time);
            })->when($is_real != '', function ($query) use ($is_real) {
                $query->where('charge_req.is_real', $is_real);
            })->orderBy('charge_req.id', 'desc')->paginate($limit);
        if ($list) {
            $items = $list->getCollection();
            $items->transform(function ($item, $key) {
                // 设置上级代理商信息
                $item->setAttribute('belong_agent_name', $item->user->belongAgent->username ?? '---');
                return $item;
            });
            $list->setCollection($items);
            foreach ($list as $v) {
                if (!stristr($v["charge_url"], 'http')) {
                    $v["charge_url"] = config('app.aws_url') . $v["charge_url"];
                }
            }
        }

        return $this->layuiData($list);
    }

    public function passReq(Request $request)
    {
        $id = $request->get('id', 0);
        $is_real = $request->get('is_real', 0);

        if (empty($id)) {
            return $this->error('参数错误');
        }
        if (empty($is_real)) {
            return $this->error('请选择入金类型');
        }
        $req = Db::table('charge_req')->where(['id' => $id, 'status' => 1])->first();
        if (!$req) {
            return $this->error('充值记录错误');
        }
        DB::table('charge_req')->where('id', $id)->update(['status' => 2, "is_real" => $is_real, 'updated_at' => date('Y-m-d H:i:s')]);
        $wallet = UsersWallet::where(['currency' => $req->currency_id, 'user_id' => $req->uid])->first();
        change_wallet_balance($wallet, 2, $req->amount, AccountLog::CHANGEREQ, '充值成功增加余额');
        try {
            event(new RechargeEvent($req->uid, 1));//充值成功增加3级内有效邀请人
        } catch (\Exception $ex) {

        }
        return $this->success('充值成功');
    }

    public function req_show(Request $request)
    {
        $id = $request->get('id', '');
        if (!$id) {
            return $this->error('参数小错误');
        }
        return view('admin.user.change_show', ["id" => $id]);
    }

    public function refuseReq(Request $request)
    {
        $id = $request->get('id', 0);
        $remark = $request->get('remark', "");
        if (empty($id)) {
            return $this->error('参数错误');
        }
        if (empty($remark)) {
            return $this->error('请输入拒绝原因');
        }
        $req = Db::table('charge_req')->where(['id' => $id, 'status' => 1])->first();
        if (!$req) {
            return $this->error('充值记录错误');
        }

        DB::table('charge_req')->where('id', $id)->update(['status' => 3, 'remark' => $remark]);
        return $this->success('拒绝成功');
    }

    public function chargeReq(Request $request)
    {
        $total_money = 0.00;
        $btc_money = 0.00;
        $eth_money = 0.00;
        $usdt_money = 0.00;
        $usdc_money = 0.00;
        if (Redis::HGETALL("user_check_ip")) {
            $uids = Redis::HGETALL("user_check_ip");
            $list = ChargeReq::whereIn("uid", $uids)->where("status", 2)->groupBy("currency_id")->selectRaw('sum(`amount`) as `total_amount` , `currency_id` ')->get()->toArray();
        } else {
            $list = ChargeReq::where("status", 2)->groupBy("currency_id")->selectRaw('sum(`amount`) as `total_amount` , `currency_id` ')->get()->toArray();
        }

        if ($list) {
            foreach ($list as $v) {
                if ($v["currency_id"] !== 3) {
                    $return = CurrencyQuotation::where(["legal_id" => 3, 'currency_id' => $v['currency_id']])->first();
                    $now_price = $return["now_price"] ?? 1;
                    $total = $v["total_amount"] * $now_price;
                    $total_money += $total;
                } else {
                    $total_money += $v["total_amount"];
                }
                if ($v["currency_id"] == 1) {
                    $btc_money = $v["total_amount"];
                } else if ($v["currency_id"] == 2) {
                    $eth_money = $v["total_amount"];
                } else if ($v["currency_id"] == 3) {
                    $usdt_money = $v["total_amount"];
                } else if ($v["currency_id"] == 28) {
                    $usdc_money = $v["total_amount"];
                }
            }
        }
        return view('admin.user.charge', ['total_money' => $total_money,
            'btc_money' => $btc_money, 'eth_money' => $eth_money,
            'usdt_money' => $usdt_money, 'usdc_money' => $usdc_money,
        ]);
    }

    public function quotation(Request $request)
    {
        $last = MyQuotation::orderBy('id', 'desc')->first();
        $currencys = CurrencyMatch::where('market_from', 3)->get();
        $itime = $last['itime']?substr($last['itime'],0,10):date('Y-m-d',time());
        return view('admin.quotation.index', ['data' => $last,"itime"=>$itime, 'currencys' => $currencys->toArray()]);
    }

    public function saveQuotation(Request $request)
    {

        $con = $request->post('kline');
        $date = $request->post('date');
        $currency = strtoupper($request->post('currency'));
        $obj = json_decode($con, true);
        $time = time();
        for ($i = 0; $i < count($obj); $i++) {
            $timeed = strtotime(date("Y-m-d H:i", ($time + $i * 60)));
            $needle = new MyQuotation();
            $needle->open = $obj[$i][1];
            $needle->high = $obj[$i][2];
            $needle->low = $obj[$i][3];
            $needle->close = $obj[$i][4];
            $needle->vol = $obj[$i][5];
            $needle->base = $currency;
            $needle->target = 'USDT';
            $needle->symbol = "{$needle->base}/{$needle->target}";
            $needle->itime = $timeed;
            $needle->save();
            $ob = json_decode(json_encode($needle), true);
            $ob['itime'] = $timeed;
            $response = RedisQuotation::set_redis_quotation($currency, $needle->target, "1min", $timeed, $ob);
        }

        return $response;
    }

    public function score(Request $request)
    {
        $id = $request->get('id', 0);
        $score = $request->get('score', 0);
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if (!$score) {
            return $this->error('请输入信用分');
        }

        try {
            $user->score = $score;
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function update_pass(Request $request)
    {
        $id = $request->get('id', 0);
        $password = $request->get('password', 0);
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if (!$password) {
            return $this->error("请输入新密码");
        }
        if (mb_strlen($password) < 6 || mb_strlen($password) > 16) {
            return $this->error('密码只能在6-16位之间');
        }
        try {
            $user->password = Users::MakePassword($password);
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function update_pay_pass(Request $request)
    {
        $id = $request->get('id', 0);
        $password = $request->get('password', 0);
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if (!$password) {
            return $this->error("请输入新密码");
        }
        if (mb_strlen($password) !== 6 || !is_numeric($password)) {
            return $this->error('密码必须是6位数字');
        }
        try {
            $user->pay_password = Users::MakePassword($password);
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function real_user(Request $request)
    {
        $id = $request->get('id', 0);

        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if ($user->is_real_user == 1) {
            $user->is_real_user = 0;
        } else {
            $user->is_real_user = 1;
        }
        try {
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function gold(Request $request)
    {
        $id = $request->get('id', 0);
        $amount = $request->get('amount', 0);
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if (!$amount) {
            return $this->error('请输入体验金额');
        }
        $info = UserGold::where("user_id", $id)->first();
        if ($info) {
            return $this->error('体验金已发放,请勿重复发放');
        }
        DB::beginTransaction();
        try {
            $wallet = UsersWallet::where("user_id", $id)->where("currency", 3)->first();
            $data_wallet['balance_type'] = 2;
            $data_wallet['lock_type'] = 0;
            $data_wallet['before'] = $wallet->change_balance;
            $data_wallet['change'] = $amount;
            $data_wallet['after'] = bc_add($wallet->change_balance, $amount, 5);
            $wallet->increment('change_balance', $amount);
            AccountLog::insertLog(['user_id' => $user->id, 'value' => $amount, 'info' => "领取体验金", 'type' => AccountLog::ADMIN_CHANGE_BALANCE, 'currency' => $wallet->currency], $data_wallet);
            $model = new UserGold();
            $model->user_id = $id;
            $model->amount = $amount;
            $model->create_time = date("Y-m-d H:i:s", time());
            $model->save();
            DB::commit();
            return $this->success('操作成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }

    }

    public function add_user(Request $request)
    {
        return view('admin.user.add');
    }

    public function do_add_user(Request $request)
    {
        $email = $request->get('email', '');
        $password = $request->get('password', '');
        if (empty($email) || empty($password)) {
            return $this->error('参数错误');
        }
        if (mb_strlen($password) < 6 || mb_strlen($password) > 16) {
            return $this->error('密码只能在6-16位之间');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('非法邮箱格式');
        }
        $user = Users::getByString($email);
        if (!empty($user)) {
            return $this->error('账号已存在');
        }
        $parent_id = 0;
        $users = new Users();
        $users->password = Users::MakePassword($password);
        $users->parent_id = $parent_id;
        $users->account_number = $email;
        $users->area_code_id = 0;
        $users->area_code = 0;
        $users->email = $email;
        $users->head_portrait = URL("mobile/images/user_head.png");
        $users->time = time();
        DB::beginTransaction();
        try {
            $users->parents_path = UserDAO::getRealParentsPath($users); // 生成parents_path tian add
            // 代理商节点id。标注该用户的上级代理商节点。这里存的代理商id是agent代理商表中的主键，并不是users表中的id。
            $agent_note_id = Agent::reg_get_agent_id_by_parentid($parent_id);
            $users->agent_note_id = $agent_note_id;
            // 代理商节点关系
            $users->agent_path = Agent::agentPath($agent_note_id);
            $users->save(); // 保存到user表中
            $users->extension_code = Users::getExtensionCode();
            $users->save();
            UsersWallet::makeWallet($users->id);
            UserProfile::unguarded(function () use ($users) {
                $users->userProfile()->create([]);
            });
            DB::commit();
            return $this->success("注册成功");
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error("注册失败");
        }

    }

    //导出用户列表至excel
    public function chargeCsv(Request $request)
    {
        $account = $request->input('account_number', '');
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $uid = $request->input('uid', '');
        $is_real = $request->input('is_real', '');
        $list = ChargeReq::join('users', 'users.id', '=', 'charge_req.uid')
            ->join('currency', 'currency.id', '=', 'charge_req.currency_id')
            ->select('charge_req.*', 'users.account_number', 'currency.name')
            ->when($account != '', function ($query) use ($account) {
                $query->where("charge_req.user_account", 'like', '%' . $account . '%');
            })->when($uid != '', function ($query) use ($uid) {
                $query->where('charge_req.uid', $uid);
            })->when($start_time != '', function ($query) use ($start_time) {
                $query->where('charge_req.created_at', '>=', $start_time);
            })->when($end_time != '', function ($query) use ($end_time) {
                $query->where('charge_req.created_at', '<=', $end_time);
            })->when($is_real != '', function ($query) use ($is_real) {
                $query->where('charge_req.is_real', $is_real);
            })->orderBy('charge_req.id', 'desc')->get()->toArray();
        $data = $list;

        return Excel::create('充币数据', function ($excel) use ($data) {
            $excel->sheet('充币数据', function ($sheet) use ($data) {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('充币ID');
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
                    $cell->setValue('充币时间');
                });
                $sheet->cell('H1', function ($cell) {
                    $cell->setValue('入金类型');
                });
                $sheet->cell('I1', function ($cell) {
                    $cell->setValue('备注');
                });

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        if ($value["status"] == 1) {
                            $status_name = "申请充值";
                        } else if ($value["status"] == 2) {
                            $status_name = "充值完成";
                        } else {
                            $status_name = "申请失败";
                        }
                        if ($value["is_real"] == 1) {
                            $is_real_name = "真实入金";
                        } else {
                            $is_real_name = "试玩/代充";
                        }
                        $i = $key + 2;
                        $sheet->cell('A' . $i, $value['id']);
                        $sheet->cell('B' . $i, $value['uid']);
                        $sheet->cell('C' . $i, $value['account_number']);
                        $sheet->cell('D' . $i, $value['name']);
                        $sheet->cell('E' . $i, $value['amount']);
                        $sheet->cell('F' . $i, $status_name);
                        $sheet->cell('G' . $i, $value['created_at']);
                        $sheet->cell('H' . $i, $is_real_name);
                        $sheet->cell('I' . $i, $value['remark']);
                    }
                }
            });
        })->download('xlsx');
    }
    public function auth(Request $request)
    {
        $id = $request->get('id', 0);
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        try {
            $user->auth_low = 2;
            $user->auth_high = 2;
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
    public function withdraw(Request $request)
    {
        $id = $request->get('id', 0);

        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        if ($user->is_withdraw == 1) {
            $user->is_withdraw = -1;
        } else {
            $user->is_withdraw = 1;
        }
        try {
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
    public function is_atelier(Request $request)
    {
        $id = $request->get('id', 0);
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('参数错误');
        }
        $vip = $request->get('vip', 0);
        if ($vip>10) {
            return $this->error('Vip等级不能大于10');
        }
        try {
            $user->is_atelier = $vip;
            $user->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

}
