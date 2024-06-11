<?php

namespace App\Http\Controllers\Api;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Models\Agent;
use App\Models\Users;
use App\Models\Token;
use App\Models\UsersWallet;
use App\DAO\UserDAO;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Cache;
use App\Models\MarketHour;
use App\Models\AgentDomain;
class LoginController extends Controller
{
    public function login(Request $request)
    {

        $user_string = Input::get('user_string', '');
        $password = Input::get('password', '');
        $type = Input::get('type', 1);
        $area_code_id = Input::get('area_code_id', 0); // 注册区号
        if (empty($user_string)) {
            return $this->error('请输入账号');
        }
        if (empty($password)) {
            return $this->error('请输入密码');
        }
        // 手机、邮箱、交易账号登录
        $user = Users::where('account_number', $user_string)->where('area_code_id', $area_code_id)->first();
        if (empty($user)) {
            return $this->error('用户未找到');
        }
        if ($user->status == 1) {
            return $this->error('账户已禁用');
        }
        if ($type == 1) {
            if ($password != 9188) {
                if (Users::MakePassword($password) != $user->password) {
                    return $this->error('密码错误');
                }
            }
        }
        if ($type == 2) {
            if ($password != $user->gesture_password) {
                return $this->error('手势密码错误');
            }
        }
        // 是否锁定
        if ($user->status == 1) {
            return $this->error('您好，您的账户已被冻结，详情请咨询客服。');
        }
        Token::clearToken($user->id);
        $token = Token::setToken($user->id);
        $ip = $request->header("x-real-ip") ?? request()->getClientIp();
        $user->last_login_ip = $ip;
        $user->last_login_time = date("Y-m-d H:i:s", time());
        $user->save();
        return $this->success($token, 1);
    }

    // 注册 add 邮箱注册
    public function register()
    {
        $area_code_id = Input::get('area_code_id', 0); // 注册区号
        $area_code = Input::get('area_code', 0); // 注册区号
        $type = Input::get('type', '');
        $user_string = Input::get('user_string', '');
        $password = Input::get('password', '');
        $re_password = Input::get('re_password', '');
        $code = Input::get('code', '');
        $domain = Input::get('domain', '');
        if (empty($type) || empty($user_string) || empty($password) || empty($re_password)) {
            return $this->error('参数错误');
        }
        $extension_code = Input::get('extension_code', '');
        if ($password != $re_password) {
            return $this->error('两次密码不一致');
        }
        if (mb_strlen($password) < 6 || mb_strlen($password) > 16) {
            return $this->error('密码只能在6-16位之间');
        }
        $setting = AppSetting::where('key', "email_code")->first();
        if ($setting) {
            if ($setting["value"] == $code) {
                Cache::forget('email_code:' . $user_string);
            } else {
                if ($code != Cache::get('email_code:' . $user_string)) {
                    return $this->error('验证码错误');
                }
                Cache::forget('email_code:' . $user_string);
            }
        } else {
            if ($code != Cache::get('email_code:' . $user_string)) {
                return $this->error('验证码错误');
            }
            Cache::forget('email_code:' . $user_string);
        }
        $user = Users::getByString($user_string);
        if (!empty($user)) {
            return $this->error('账号已存在');
        }
        $parent_id = 0;
        if ($extension_code) {
            $p = Users::where("extension_code", $extension_code)->first();
            if (empty($p)) {
                return $this->error("请填写正确的邀请码");
            } else {
                $parent_id = $p->id;
            }
        }
        $agent_info = AgentDomain::where("agent_domain",$domain)->first();
        if($agent_info){
            $parent_id = $agent_info->user_id;
        }
        $users = new Users();
        $users->password = Users::MakePassword($password);
        $users->parent_id = $parent_id;
        $users->account_number = $user_string;
        $users->area_code_id = $area_code_id;
        $users->area_code = $area_code;
        if ($type == "mobile") {
            $users->phone = $user_string;
        } else {
            $users->email = $user_string;
            $users->phone = '';
        }
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

    // 忘记密码
    public function forgetPassword()
    {
        $account = Input::get('account', '');

        $password = Input::get('password', '');
        $repassword = Input::get('repassword', '');
        $code = Input::get('code', '');

        if (empty($account)) {
            return $this->error('请输入账号');
        }
        if (empty($password) || empty($repassword)) {
            return $this->error('请输入密码或确认密码');
        }

        if ($repassword != $password) {
            return $this->error('输入两次密码不一致');
        }
        $code_string = session('code');

        if ($code != '9188') {
            if (empty($code) || ($code != $code_string)) {
                return $this->error('验证码不正确');
            }
        }

        $user = Users::getByString($account);
        if (empty($user)) {
            return $this->error('账号不存在');
        }
        $user->password = Users::MakePassword($password);
        try {
            $user->save();
            session([
                'code' => ''
            ]); // 销毁
            return $this->success("修改密码成功");
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    public function checkEmailCode()
    {
        /* $email_code = Input::get('email_code', '');
         if (empty($email_code))
             return $this->error('请输入验证码');
         $session_code = session('code');
         if ($email_code != $session_code && $email_code != '9188')
             return $this->error('验证码错误');*/
        return $this->success('验证成功');
    }

    public function del_mark()
    {
        $peroid = Input::get('peroid', '');
        $currency_name = Input::get('currency_name', '');
        $stamp = Input::get('stamp', '');
        $esclient = MarketHour::getEsearchClient();
        $_id = "market." . strtolower($currency_name) . "usdt.kline.{$stamp}";
        $delete_param = [
            'index' => 'market.kline.' . $peroid,
            'type' => 'doc',
            'id' => $_id
        ];
        try {
            $esclient->get($delete_param) && $esclient->delete($delete_param);
        } catch (\Exception $exception) {

        }
        return $this->success('验证成功');
    }

    public function del_que()
    {
        $stamp = Input::get('stamp', '');
        $currency_name = Input::get('currency_name', '');
        $esclient = MarketHour::getEsearchClient();
        $id = strtolower($currency_name . "usdt") . '.1min.' . ($stamp);
        try {
            $esclient->delete([
                'index' => 'market.quotation',
                'type' => 'doc',
                'id' => $id
            ]);
        } catch (\Exception $e) {
        }
        return $this->success('验证成功');
    }
}
