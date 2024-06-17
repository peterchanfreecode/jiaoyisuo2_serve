<?php

/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Users extends Model
{
    protected $table = 'users';
    public $timestamps = false;

    protected $hidden = [
        'password',
        'pay_password',
        'memorizing_words',
        'is_blacklist',
        'gesture_password',
        'risk',
    ];

    protected $appends = [
        'account',
        'is_seller',
        'risk_name',
        'create_date',
        'usdt',
        'caution_money',
        'parent_name',
        'my_agent_level',
        'my_en_agent_level',
        'userreal_name', //tian add
        'usdt_mic',
    ];

    protected static $roleList = [
        MicroOrder::RESULT_LOSS => '亏损',
        MicroOrder::RESULT_BALANCE => '无',
        MicroOrder::RESULT_PROFIT => '盈利',
        MicroOrder::UP_WIN => '涨赢',
        MicroOrder::LOSE => '跌赢',
    ];

    public function getUserrealNameAttribute()
    {
        $user_profile = $this->userReal()->first();
        if ($user_profile) {
            return $user_profile->name ?? '--';
        } else {
            return '--';
        }

    }

    public function userReal()
    {
        return $this->hasMany(UserReal::class, 'user_id')->where('status', 2);
    }

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function getLeverBalanceAttribute()
    {
        $id = $this->getAttribute('id');
        if (empty($id)) {
            return '';
        }
        $wallet = UsersWallet::where('user_id', $id)->where('currency', 3)->first();
        return $wallet->lever_balance;
    }

    public function getLockLeverBalanceAttribute()
    {
        $id = $this->getAttribute('id');
        if (empty($id)) {
            return '';
        }
        $wallet = UsersWallet::where('user_id', $id)->where('currency', 3)->first();
        return $wallet->lock_lever_balance;
    }

    public function getLegalBalanceAttribute()
    {
        $id = $this->getAttribute('id');
        if (empty($id)) {
            return '';
        }
        $wallet = UsersWallet::where('user_id', $id)->where('currency', 3)->first();
        return $wallet->legal_balance;
    }

    public function getLockLegalBalanceAttribute()
    {
        $id = $this->getAttribute('id');
        $wallet = UsersWallet::where('user_id', $id)->where('currency', 3)->first();
        return $wallet->lock_legal_balance;
    }

    public function getUsdtMicAttribute()
    {
        $value = $this->getAttribute('id');
        if (empty($value)) {
            return '';
        }
        $us = DB::table('currency')->where('name', 'USDT')->first();

        $wal = UsersWallet::where('currency', $us->id)->where('user_id', $value)->first();

        return isset($wal->micro_balance) ? $wal->micro_balance : '0.00000';
    }

    //秒合约账号
    public function getUsdtAttribute()
    {
        $value = $this->getAttribute('id');
        if (empty($value)) {
            return '';
        }
        $us = DB::table('currency')->where('name', 'USDT')->first();

        $wal = UsersWallet::where('currency', $us->id)->where('user_id', $value)->first();

        return isset($wal->lever_balance) ? $wal->lever_balance : '0.00000';
    }

    public function getCautionMoneyAttribute()
    {
        $value = $this->getAttribute('id');
        if (empty($value)) {
            return '';
        }
        return DB::table('lever_transaction')->where('user_id', $value)->whereIn('status', [0, 1])->sum('caution_money');
    }

    public function getParentNameAttribute()
    {
        $value = $this->getAttribute('parent_id');
        $p = self::where('id', $value)->first();
        return isset($p->account_number) ? $p->account_number : '-/-';
    }

    public function getMyAgentLevelAttribute()
    {
        $value = $this->attributes['agent_id'] ?? 0;
        if ($value == 0) {
            return '普通用户';
        } else {
            $m = DB::table('agent')->where('id', $value)->first();
            $name = '';
            if (empty($m)) {
                $name = '';
            } else {
                if ($m->level == 0) {
                    $name = '超管';
                } else if ($m->level > 0) {
                    $name = $m->level . '级代理商';
                }
            }

            return $name;
        }
    }

    public function getMyEnAgentLevelAttribute()
    {
        $value = $this->attributes['agent_id'] ?? 0;
        if ($value == 0) {
            return 'general user';
        } else {
            $m = DB::table('agent')->where('id', $value)->first();
            $name = '';
            if (empty($m)) {
                $name = '';
            } else {
                if ($m->level == 0) {
                    $name = 'super tube';
                } else if ($m->level > 0) {
                    $name = "Tier " . $m->level . ' agent';
                }
            }

            return $name;
        }
    }

    public function getCreateDateAttribute()
    {
        $value = $this->getAttribute('time');
        return $value;
        return date('Y-m-d H:i:s', $value);
    }

    //密码加密
    public static function MakePassword($password, $type = 0)
    {
        if ($type == 0) {
            $salt = 'ABCDEFG';
            $passwordChars = str_split($password);
            foreach ($passwordChars as $char) {
                $salt .= md5($char);
            }
        } else {
            $salt = 'TPSHOP' . $password;
        }
        return md5($salt);
    }

    public static function getByAccountNumber($account_number)
    {
        return self::where('account_number', $account_number)->first();

    }

    public static function getByString($string)
    {
        if (empty($string)) {
            return "";
        }
        return self::where("phone", $string)
            ->orwhere('email', $string)
            ->orWhere('account_number', $string)
            ->first();
    }

    public static function getById($id)
    {
        if (empty($id)) {
            return "";
        }
        return self::where("id", $id)->first();
    }

    //生成邀请码
    public static function getExtensionCode()
    {
        $code = self::generate_password(4);
        if (self::where("extension_code", $code)->first()) {
            //如果生成的邀请码存在，继续生成，直到不存在
            $code = self::getExtensionCode();
        }
        return $code;
    }

    public static function generate_password($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $password = "";
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    public static function getUserId()
    {
        // return session('user_id');
        $token = Token::getToken();
        $user_id = Token::getUserIdByToken($token);
        return $user_id;
    }

    public static function getAuthUser()
    {
        return self::find(self::getUserId());
    }


    public function getTimeAttribute()
    {
        if (isset($this->attributes['time'])) {
            $value = $this->attributes['time'];
            return $value ? date('Y-m-d H:i:s', $value) : '';
        } else {
            return "";
        }
    }

    //获取用户的账号  手机号或邮箱
    public function getAccountAttribute()
    {
        //$value = $this->attributes['phone'];
        $value = $this->getAttribute('phone');
        if (empty($value)) {
            $value = $this->getAttribute('email');
            if (empty($value)) {
                return '';
            }
            $n = strripos($value, '@');
            $value = mb_substr($value, 0, 2) . '******' . mb_substr($value, $n);
        } else {
            $value = mb_substr($value, 0, 3) . '******' . mb_substr($value, -3, 3);
        }
        return $value;
    }

    /*
    //手势密码序列化
    public function setGesturePassword($value) {
        $this->attributes['gesture_password'] = serialize($value);
    }
    //取出数据时反序列化
    public function getGesturePassword($value) {
        return unserialize($value);
    }
    */

    public function getIsSellerAttribute()
    {

        $id =  $this->getAttribute('id');
        if (empty($id)) {
            return 0;
        }
        $seller = Seller::where('user_id',$id)->first();
        if (!empty($seller)) {
            return 1;
        }
        return 0;
    }

    public function cashinfo()
    {
        return $this->belongsTo('App\UserCashInfo', 'id', 'user_id');
    }

    public function legalDeal()
    {
        return $this->hasOne('App\C2cDeal', 'seller_id', 'id');
    }

    /**
     *
     * @param  $model 用户模型实例
     * @param  $status 锁定开关
     * @param  $time 锁定结束时间
     * @return bool
     */
    public function lockUser($model, $status, $time)
    {
        if (!empty($time)) {
            $time = strtotime($time);
        }
        if ($status == 1) {
            $model->status = 1;
            $model->lock_time = $time;
        } else {
            $model->status = 0;
            $model->lock_time = 0;
        }
        $result = $model->save();
        if ($result) {
            return true;
        }
        return false;
    }

    public function getRiskNameAttribute()
    {
        $risk = $this->attributes['risk'] ?? 0;
        return self::$roleList[$risk];
    }

    /*
     * count 当前几代
     * $algebra 总共几代
     * user_id 用户id
     * touch_user_id 触发者id
     * currency 币种id
     * price 金额
     * */
    public static function rebate($user_id, $touch_user_id, $currency, $price, $count = 1, $algebra = 0)
    {
        $user = self::where('id', $user_id)->first();
        $touch_user = self::getById($touch_user_id);
        if (empty($user)) {
            return true;
        }

        if ($user->parent_id == 0) {
            return true;
        }
        $wallet = UsersWallet::where('currency', $currency)
            ->where('user_id', $user->parent_id)
            ->first();

        $u_algebra = Algebra::where('algebra', $count)->first();
        if (empty($u_algebra) || empty($wallet)) {
            $count += 1;
            $algebra -= 1;
            $result = self::rebate($user->parent_id, $touch_user_id, $currency, $price, $count, $algebra);
            return $result;
        }

        $totle_price = $price * $u_algebra->rate / 100;
        $info = '第' . $count . "代用户{$touch_user->account_number}返手续费：" . $totle_price;
        $result = change_wallet_balance($wallet, 2, $totle_price, AccountLog::MICRO_TRADE_CLOSE_SETTLE, $info);
        $algebra -= 1;
        $user_algebra = new UserAlgebra();
        $user_algebra->user_id = $user->parent_id;
        $user_algebra->touch_user_id = $touch_user_id;
        $user_algebra->algebra = $count;
        $user_algebra->info = $info;
        $user_algebra->value = $totle_price;
        $user_algebra->save();
        $count += 1;
        if ($algebra == 0) {
            return true;
        } else {
            $result = self::rebate($user->parent_id, $touch_user_id, $currency, $price, $count, $algebra);
            return $result;
        }

    }

    public function belongAgent()
    {
        return $this->belongsTo(Agent::class, 'agent_note_id', 'id');
    }

    public static function get_lists($list)
    {
        if ($list) {
            foreach ($list as $v) {
                $user_str = '<span>用户编号: ' . $v->id . "</span>" . "<br>";
                $str = $v->agent_id > 0 ? "<span style='color: red'>是</span>" : "否" . "</span>";
                $user_str .= '<span>是否代理: ' . $str . "</span>" . "<br>";
                $user_str .= '<span>代理ID: ' . $v->agent_id . "</span>" . "<br>";
                $user_str .= '<span>登录帐号: ' . $v->account_number . "</span>" . "<br>";
                $info = time() - strtotime($v->last_active_time) < 1800 ? "<span style='color: red'>在线</span>" : "离线" . "</span>";
                $user_str .= '<span>在线状态: ' . $info . "</span>" . "<br>";
                $user_str .= '<span>认证姓名: ' . $v->userreal_name . "</span>" . "<br>";
                $v["user_info"] = $user_str;
                $user_str = '<span>注册时间: ' . $v->time . "</span>" . "<br>";
                $user_str .= '<span>最后登录IP: ' . $v->last_login_ip . "</span>" . "<br>";
                $user_str .= '<span>最后登录时间: ' . $v->last_login_time . "</span>" . "<br>";
                $user_str .= '<span>最后在线时间: ' . $v->last_active_time . "</span>" . "<br>";
                $v["time_info"] = $user_str;

                $user_str = "<span>注册类型: 邮件注册</span><br>";
                $user_str .= "<span>语言类型: English</span><br>";

                $user_str .= '<span>上级代理: ' . $v->parent_name . "</span>" . "<br>";
                $user_str .= '<span>邀请码: ' . $v->extension_code . "</span>" . "<br>";
                $user_str .= '<span>风控类型: ' . $v->risk_name . "</span> <a class='layui-btn layui-btn-warm layui-btn-xs' lay-event='edit'>风控配置</a>" . "<br>";
                $user_str .= " <a class='layui-btn layui-btn-warm layui-btn-xs' lay-event='micro'>利率控制</a>" . "<br>";
                if ($v->status == 0) {
                    $user_str .= "<span>帐号状态: </span><span style='color: green'>启用  </span><a class='layui-btn layui-btn-warm layui-btn-xs' lay-event='status_close'>禁用</a>";
                } else if ($v->status == 1) {
                    $user_str .= "<span>帐号状态: </span><span style='color: red'>禁用  </span><a class='layui-btn layui-btn-warm layui-btn-xs' lay-event='status_open'>启用</a>";
                }

                $v["invite_info"] = $user_str;
                $withdraw = 0;
                $withdraw_list = UsersWalletOut::where("user_id", $v->id)->where("status", 2)->select("currency", "user_id",DB::raw('SUM(number) as total_num'))->groupBy("currency")->get()->toArray();
                if ($withdraw_list) {
                    foreach ($withdraw_list as $val) {
                        $currency_info = Currency::where("id", $val["currency"])->first();
                        if ($val["currency"] == 3) {
                            $withdraw = bc_add($withdraw, $val["total_num"], 6);
                        } else {
                            $withdraw = bc_add($withdraw, $val["total_num"] * $currency_info["price"], 6);
                        }

                    }
                }
                $user_str = "<span>总提现(折合USDT): " . $withdraw . " </span><br/>";
                $charge = 0;
                $charge_list = ChargeReq::where("uid", $v->id)->where("status", 2)->select("currency_id", DB::raw('SUM(amount) as total_amount'))->groupBy("currency_id")->get()->toArray();
                if ($charge_list) {
                    foreach ($charge_list as $val) {
                        $currency_info = Currency::where("id", $val["currency_id"])->first();
                        if ($val["currency_id"] == 3) {
                            $charge = bc_add($charge, $val["total_amount"], 6);
                        } else {
                            $charge = bc_add($charge, $val["total_amount"] * $currency_info["price"], 6);
                        }
                    }
                }
                $user_str .= "<span>总充值(折合USDT): " . $charge . "</span><br/>";
                $usdt_info = UsersWallet::where("user_id", $v->id)->where("currency", 3)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>USDT余额: " . $usdt_info["change_balance"] . "</span><br/>";
                $btc_info = UsersWallet::where("user_id", $v->id)->where("currency", 1)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>BTC余额: " . $btc_info["change_balance"] . "</span><br/>";
                $eth_info = UsersWallet::where("user_id", $v->id)->where("currency", 2)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>ETH余额: " . $eth_info["change_balance"] . "</span><br/>";
                $usdc_info = UsersWallet::where("user_id", $v->id)->where("currency", 28)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>USDC余额: " . $usdc_info["change_balance"] . "</span><br/>";
                $v["money_info"] = $user_str;

                $user_str = "<span>USDT: " . $usdt_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>BTC: " . $btc_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>ETH: " . $eth_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>USDC: " . $usdc_info["lock_change_balance"] . "</span><br/>";
                $v["lock_info"] = $user_str;
            }
        }
        return $list;
    }

    public static function get_en_lists($list)
    {
        if ($list) {
            foreach ($list as $v) {
                $user_str = '<span>user ID: ' . $v->id . "</span>" . "<br>";
                $str = $v->agent_id > 0 ? "<span style='color: red'>yes</span>" : "no" . "</span>";
                $user_str .= '<span>Whether to act as an agent: ' . $str . "</span>" . "<br>";
                $user_str .= '<span>Proxy ID: ' . $v->agent_id . "</span>" . "<br>";
                $user_str .= '<span>log in account: ' . $v->account_number . "</span>" . "<br>";
                $info = time() - strtotime($v->last_active_time) < 1800 ? "<span style='color: red'>online</span>" : "offline" . "</span>";
                $user_str .= '<span>online status: ' . $info . "</span>" . "<br>";
                $user_str .= '<span>Certification name: ' . $v->userreal_name . "</span>" . "<br>";
                $v["user_info"] = $user_str;
                $user_str = '<span>Registration time: ' . $v->time . "</span>" . "<br>";
                $user_str .= '<span>Last login IP: ' . $v->last_login_ip . "</span>" . "<br>";
                $user_str .= '<span>last login time: ' . $v->last_login_time . "</span>" . "<br>";
                $user_str .= '<span>last online time: ' . $v->last_active_time . "</span>" . "<br>";
                $v["time_info"] = $user_str;

                $user_str = "<span>Registration Type: Email Registration</span><br>";
                $user_str .= "<span>language type: English</span><br>";

                $user_str .= '<span>superior agent: ' . $v->parent_name . "</span>" . "<br>";
                $user_str .= '<span>Invitation code: ' . $v->extension_code . "</span>" . "<br>";
                $user_str .= '<span>Risk control type: ' . $v->risk_name . "</span> <a class='layui-btn layui-btn-warm layui-btn-xs' lay-event='edit'>Risk control configuration</a>" . "<br>";
                $user_str .= " <a class='layui-btn layui-btn-warm layui-btn-xs' lay-event='micro'>interest rate control</a>" . "<br>";
                if ($v->status == 0) {
                    $user_str .= "<span>account status: </span><span style='color: green'>enable  </span><a class='layui-btn layui-btn-warm layui-btn-xs' lay-event='status_close'>disabled</a>";
                } else if ($v->status == 1) {
                    $user_str .= "<span>account status: </span><span style='color: red'>disabled  </span><a class='layui-btn layui-btn-warm layui-btn-xs' lay-event='status_open'>enable</a>";
                }

                $v["invite_info"] = $user_str;
                $withdraw = 0;
                $withdraw_list = UsersWalletOut::where("user_id", $v->id)->where("status", 2)->select("currency", "user_id",DB::raw('SUM(number) as total_num'))->groupBy("currency")->get()->toArray();
                if ($withdraw_list) {
                    foreach ($withdraw_list as $val) {
                        $currency_info = Currency::where("id", $val["currency"])->first();
                        if ($val["currency"] == 3) {
                            $withdraw = bc_add($withdraw, $val["total_num"], 6);
                        } else {
                            $withdraw = bc_add($withdraw, $val["total_num"] * $currency_info["price"], 6);
                        }

                    }
                }
                $user_str = "<span>Total withdrawal (equivalent to USDT): " . $withdraw . " </span><br/>";
                $charge = 0;
                $charge_list = ChargeReq::where("uid", $v->id)->where("status", 2)->select("currency_id", DB::raw('SUM(amount) as total_amount'))->groupBy("currency_id")->get()->toArray();
                if ($charge_list) {
                    foreach ($charge_list as $val) {
                        $currency_info = Currency::where("id", $val["currency_id"])->first();
                        if ($val["currency_id"] == 3) {
                            $charge = bc_add($charge, $val["total_amount"], 6);
                        } else {
                            $charge = bc_add($charge, $val["total_amount"] * $currency_info["price"], 6);
                        }
                    }
                }
                $user_str .= "<span>Total deposit (equivalent to USDT): " . $charge . "</span><br/>";
                $usdt_info = UsersWallet::where("user_id", $v->id)->where("currency", 3)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>USDT balance: " . $usdt_info["change_balance"] . "</span><br/>";
                $btc_info = UsersWallet::where("user_id", $v->id)->where("currency", 1)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>BTC balance: " . $btc_info["change_balance"] . "</span><br/>";
                $eth_info = UsersWallet::where("user_id", $v->id)->where("currency", 2)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>ETH balance: " . $eth_info["change_balance"] . "</span><br/>";
                $usdc_info = UsersWallet::where("user_id", $v->id)->where("currency", 28)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>USD balance: " . $usdc_info["change_balance"] . "</span><br/>";
                $v["money_info"] = $user_str;

                $user_str = "<span>USDT: " . $usdt_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>BTC: " . $btc_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>ETH: " . $eth_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>USDC: " . $usdc_info["lock_change_balance"] . "</span><br/>";
                $v["lock_info"] = $user_str;
            }
        }
        return $list;
    }
    public static function get_admin_lists($list)
    {
        if ($list) {
            foreach ($list as $v) {
                $user_str = '<span>用户编号: ' . $v->id . "</span>" . "<br>";
                $str = $v->agent_id > 0 ? "<span style='color: red'>是</span>" : "否" . "</span>";
                $user_str .= '<span>是否代理: ' . $str . "</span>" . "<br>";
                $user_str .= '<span>代理ID: ' . $v->agent_id . "</span>" . "<br>";
                $user_str .= '<span>登录帐号: ' . $v->account_number . "</span>" . "<br>";
                $info = time() - strtotime($v->last_active_time) < 20 ? "<span style='color: red'>在线</span>" : "离线" . "</span>";
                $user_str .= '<span>在线状态: ' . $info . "</span>" . "<br>";
                $user_str .= '<span>认证姓名: ' . $v->userreal_name . "</span>" . "<br>";
                $user_str .= '<span>信用分: ' . $v->score . "</span>" . "<br>";
                $v["user_info"] = $user_str;

                $user_str = '<span>注册时间: ' . $v->time . "</span>" . "<br>";
                $user_str .= '<span>最后登录IP: ' . $v->last_login_ip . "</span>" . "<br>";
                $user_str .= '<span>最后登录时间: ' . $v->last_login_time . "</span>" . "<br>";
                $user_str .= '<span>最后在线时间: ' . $v->last_active_time . "</span>" . "<br>";
                $v["time_info"] = $user_str;
                $user_str = '<span>直属邀请: ' . $v->zhitui_real_number . "</span>" . "<br>";
                $user_str .= '<span>3代内邀请: ' . $v->real_teamnumber . "</span>" . "<br>";
                $user_str .= '<span>新币邀请: ' . $v->new_currery_number . "</span>" . "<br>";
                $user_str .= '<span>锁仓金额: ' . $v->candy_number . "</span>" . "<br>";
                $v["yao_info"] = $user_str;
                $user_str = '<span>上级代理: ' . $v->parent_name . "</span>" . "<br>";
                $user_str .= '<span>邀请码: ' . $v->extension_code . "</span>" . "<br>";
                $user_str .= '<span>风控类型: ' . $v->risk_name . "</span><br>";
             
                if ($v->status == 0) {
                    $user_str .= "<span>帐号状态: </span><span style='color: green'>启用  </span>";
                } else if ($v->status == 1) {
                    $user_str .= "<span>帐号状态: </span><span style='color: red'>禁用  </span>";
                }
                $v["invite_info"] = $user_str;
                $withdraw = 0;
                $withdraw_list = UsersWalletOut::where("user_id", $v->id)->where("status", 2)->select("currency", "user_id",DB::raw('SUM(number) as total_num'))->groupBy("currency")->get()->toArray();
                if ($withdraw_list) {
                    foreach ($withdraw_list as $val) {
                        $currency_info = Currency::where("id", $val["currency"])->first();
                        if ($val["currency"] == 3) {
                            $withdraw = bc_add($withdraw, $val["total_num"], 6);
                        } else {
                            $withdraw = bc_add($withdraw, $val["total_num"] * $currency_info["price"], 6);
                        }

                    }
                }
                $user_str = "<span>总提现(折合USDT): " . $withdraw . " </span><br/>";
                $charge = 0;
                $charge_list = ChargeReq::where("uid", $v->id)->where("status", 2)->select("currency_id", DB::raw('SUM(amount) as total_amount'))->groupBy("currency_id")->get()->toArray();
                if ($charge_list) {
                    foreach ($charge_list as $val) {
                        $currency_info = Currency::where("id", $val["currency_id"])->first();
                        if ($val["currency_id"] == 3) {
                            $charge = bc_add($charge, $val["total_amount"], 6);
                        } else {
                            $charge = bc_add($charge, $val["total_amount"] * $currency_info["price"], 6);
                        }
                    }
                }
                $user_str .= "<span>总充值(折合USDT): " . $charge . "</span><br/>";
                $usdt_info = UsersWallet::where("user_id", $v->id)->where("currency", 3)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>USDT余额: " . $usdt_info["change_balance"] . "</span><br/>";
                $btc_info = UsersWallet::where("user_id", $v->id)->where("currency", 1)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>BTC余额: " . $btc_info["change_balance"] . "</span><br/>";
                $eth_info = UsersWallet::where("user_id", $v->id)->where("currency", 2)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>ETH余额: " . $eth_info["change_balance"] . "</span><br/>";
                $usdc_info = UsersWallet::where("user_id", $v->id)->where("currency", 28)->select("change_balance", "lock_change_balance")->first();
                $user_str .= "<span>USDC余额: " . $usdc_info["change_balance"] . "</span><br/>";
                $v["money_info"] = $user_str;

                $user_str = "<span>USDT: " . $usdt_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>BTC: " . $btc_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>ETH: " . $eth_info["lock_change_balance"] . "</span><br/>";
                $user_str .= "<span>USDC: " . $usdc_info["lock_change_balance"] . "</span><br/>";
                $v["lock_info"] = $user_str;
            }
        }
        return $list;
    }
}
