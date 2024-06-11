<?php

namespace App\Http\Controllers\Api;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use App\Models\Users;
use App\Models\Token;
use App\Models\UserReal;
use App\Models\Currency;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\UsersWallet;
use App\Models\RebateFlow;
use App\Models\UserAlgebra;
use App\Jobs\SendEmail;
use App\Models\UserRealHigh;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class UserController extends Controller
{
    public function checkPayPassword()
    {
        $password = Input::get('password', '');
        $user = Users::getById(Users::getUserId());
        if ($user->pay_password != Users::MakePassword($password)) {
            return $this->error('密码错误');
        } else {
            return $this->success('操作成功');
        }
    }

    //退出
    public function logout()
    {

        $user_id = Users::getUserId();
        $user = Users::find($user_id);

        if (empty($user)) {
            return $this->error("会员未找到");
        }
        //清除用户的token  session
        session(['user_id' => '']);
        $token = Token::getToken();
        //删除当前token
        Token::deleteToken($user_id, $token);

        return $this->success('退出登录成功');


    }

    //我的
    public function info(Request $request)
    {
        $request_user_id = $request->get('user_id', 0);
        $user_id = Users::getUserId();
        if ($request_user_id) {
            $user_id = $request_user_id;
        }
        $currency_usdt_id = Currency::where('name', 'USDT')->select(['id', 'name'])->first();
        $user = Users::where("id", $user_id)->first();
        if (empty($user)) {
            return $this->error("会员未找到");
        }
        $user['is_open_transfer_candy'] = Setting::getValueByKey("is_open_transfer_candy");
        //用户认证状况
        $res = UserReal::where('user_id', $user_id)->first();
        if (empty($res)) {
            $user['review_status'] = 0;
            $user['name'] = '';
        } else {
            $user['review_status'] = $res['status'];
            $user['name'] = $res['name'];
        }
        $seller = Seller::where('user_id', $user_id)->get()->toArray();
        if (!empty($seller)) {
            $user['seller'] = $seller;
        }
        $user['tobe_seller_lockusdt'] = Setting::getValueByKey("tobe_seller_lockusdt");
        $user['currency_usdt_id'] = $currency_usdt_id->id;
        $user['currency_usdt_name'] = $currency_usdt_id->name;
        //幣幣钱包
        $change_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currencyCoin', function ($query) {
                $query->where('is_match', 1);
            })->get(['id', 'currency', 'change_balance', 'lock_change_balance'])->toArray();
        $user["change_wallet"] = $change_wallet;
        return $this->success($user);
    }

    //身份认证
    public function realName()
    {

        $user_id = Users::getUserId();
        $name = Input::get("name", "");//真实姓名
        $card_id = Input::get("card_id", "");//身份证号
        $front_pic = Input::get("front_pic", "");//正面照片
        $reverse_pic = Input::get("reverse_pic", "");//反面照片
        if (empty($name) || empty($card_id) || empty($front_pic) || empty($reverse_pic)) {
            return $this->error("请提交完整的信息");
        }
        $user = Users::find($user_id);
        if (empty($user)) {
            return $this->error("会员未找到");
        }
        $userreal_number = UserReal::where("card_id", $card_id)->count();
        if ($userreal_number > 0) {
            return $this->error("该身份证号已实名认证过!");
        }
        $userreal = UserReal::where('user_id', $user_id)->first();
        if (!empty($userreal)) {
            return $this->error("您已经申请过了");
        }
        try {

            $userreal = new UserReal();

            $userreal->user_id = $user_id;
            $userreal->name = $name;
            $userreal->card_id = $card_id;
            $userreal->create_time = time();
            $userreal->front_pic = $front_pic;
            $userreal->reverse_pic = $reverse_pic;
//            $userreal->hand_pic = $hand_pic;

            $userreal->save();

            return $this->success('提交成功，等待审核');
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }


    }

    // 初级认证
    public function authLow()
    {
        $user_id = Users::getUserId();
        $type = Input::get("type", 1);//类型
        $name = Input::get("name", "");//真实姓名
        $card_id = Input::get("card_id", "");//身份证号
        $front_pic = Input::get("front_pic", "");//正面照片
        $reverse_pic = Input::get("reverse_pic", "");//反面照片
        if (empty($name) || empty($card_id) || empty($front_pic) || empty($reverse_pic)) {
            return $this->error("请提交完整的信息");
        }

        $user = Users::find($user_id);
        if (empty($user)) {
            return $this->error("会员未找到");
        }
        $userreal_number = UserReal::where("card_id", $card_id)->where("status", 2)->count();
        if ($userreal_number > 0) {
            return $this->error("该身份证号已实名认证过!");
        }
        $userreal = UserReal::where('user_id', $user_id)->where("status", 2)->orderBy('id', 'desc')->first();
        if ($userreal) {
            return $this->error("您已经认证过了!");
        }
        try {
            $userreal = new UserReal();
            $userreal->user_id = $user_id;
            $userreal->type = $type;
            $userreal->name = $name;
            $userreal->card_id = $card_id;
            $userreal->create_time = time();
            $userreal->front_pic = $front_pic;
            $userreal->reverse_pic = $reverse_pic;
            $userreal->save();
            Users::where('id', $user_id)->update(['auth_low' => 1]);
            return $this->success('成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    // 高级认证
    public function authHigh()
    {
        $user_id = Users::getUserId();
        $hand_pic = Input::get("hand_pic", "");//手持身份证照片
        $user = Users::find($user_id);
        if (empty($user)) {
            return $this->error("会员未找到");
        }
        if ($user['auth_high'] == 2) {
            return $this->error("您已经认证过了!");
        }
        $userreal = UserReal::where('user_id', $user_id)->where("status", 2)->first();
        if (!$userreal) {
            return $this->error("请先完成初级认证");
        }

        $userreal_high = UserRealHigh::where('user_id', $user_id)->where("status", 2)->orderBy('id', 'desc')->first();
        if ($userreal_high) {
            return $this->error("您已经认证过了!");
        }
        try {
            $userreal_high = new UserRealHigh();
            $userreal_high->user_id = $user_id;
            $userreal_high->name = $userreal['name'];
            $userreal_high->card_id = $userreal['card_id'];
            $userreal_high->create_time = time();
            $userreal_high->hand_pic = $hand_pic;
            $userreal_high->save();
            Users::where('id', $user_id)->update(['auth_high' => 1]);
            return $this->success('成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    //个人中心  身份认证信息
    public function userCenter()
    {
        $user_id = Users::getUserId();
        $user = Users::where("id", $user_id)->first(['id', 'phone', 'email']);
        if (empty($user)) {
            return $this->error("会员未找到");
        }
        $userreal = UserReal::where('user_id', $user_id)->first();
        if (empty($userreal)) {
            $user['review_status'] = 0;
            $user['name'] = '';
            $user['card_id'] = '';
        } else {
            $user['review_status'] = $userreal['status'];
            $user['name'] = $userreal['name'];
            $user['card_id'] = $userreal['card_id'];

        }
        if (!empty($user['card_id'])) {
            $user['card_id'] = mb_substr($user['card_id'], 0, 2) . '******' . mb_substr($user['card_id'], -2, 2);
        }
        return $this->success($user);

    }

    public function mining()
    {
        $user_id = Users::getUserId();
        $user = Users::where('id', $user_id)->first();
        $num = UserAlgebra::where('user_id', $user_id)->sum('value');
        $count = Users::where('parent_id', $user_id)->where('level', '>=', 1)->count('id');
        $level = $user->level;
        $sum = Users::whereRaw("FIND_IN_SET(" . $user_id . ",parents_path)")->count('id');
        $data['sum'] = $sum;
        $data['count'] = $count;
        $data['level'] = $level;
        $data['num'] = $num;
        return $this->success($data);
    }

    // 发送邮件
    public function sendMail(Request $request)
    {
        $mail = $request->get('mail');
        if (empty($mail)) {
            return $this->error('邮箱不能为空');
        }
        $user = Users::getByString($mail);
        if (!empty($user)) {
            return $this->error('邮箱已注册');
        }
        $key = strtotime(date("Y-m-d",time()))."_".$mail;
        $num = Redis::get($key)??0;
        if($num<10){
            SendEmail::dispatch($mail)->onQueue('email')->onConnection('redis');
            Redis::set($key,$num+1,"EX",86400);
            return $this->success('发送成功');
        }else{
            return $this->error('发送超出上限');
        }
    }

    // 验证
    public function checkCode(Request $request)
    {
        $mail = $request->get('mail', '');
        $code = $request->get('code', '');
        if (empty($mail)) {
            return $this->error('邮箱不能为空');
        }
        if (empty($code)) {
            return $this->error('请输入验证码');
        }
        $setting = AppSetting::where('key', "email_code")->first();
        if ($setting) {
            if ($setting["value"] == $code) {
                return $this->success('验证成功');
            }
        }
        $cacheCode = Cache::get('email_code:' . $mail);
        if (empty($cacheCode) || $cacheCode != $code) {
            return $this->error('验证码错误');
        }
        return $this->success('验证成功');
    }

    // 支付密码
    public function set_pay_password(Request $request)
    {
        $old_pass = $request->get('old_pass', '');
        $new_pass = $request->get('new_pass', '');
        $re_new_pass = $request->get('re_new_pass', '');
        $user_id = Users::getUserId();
        $user = Users::where('id', $user_id)->first();
        if (!$user) {
            return $this->error("会员未找到");
        }
        if ($user->pay_password) {
            if (!$old_pass) {
                return $this->error("请输入旧支付密码");
            }
            $old_pass_str = Users::MakePassword($old_pass);
            if ($user->pay_password != $old_pass_str) {
                return $this->error("旧密码错误");
            }
        }
        if ($new_pass != $re_new_pass) {
            return $this->error('两次密码不一致');
        }
        if (mb_strlen($new_pass) !== 6 || !is_numeric($new_pass)) {
            return $this->error('密码必须是6位数字');
        }
        $user->pay_password = Users::MakePassword($new_pass);
        if ($user->save()) {
            return $this->success('成功');
        } else {
            return $this->error('失败');
        }

    }

    // 支付密码
    public function is_set_pay_pass()
    {

        $user_id = Users::getUserId();
        $user = Users::where('id', $user_id)->first();
        if (!$user) {
            return $this->error("会员未找到");
        }
        if ($user->pay_password) {
            return $this->success(1);
        } else {
            return $this->success(0);
        }
    }

    // 支付密码
    public function update_password(Request $request)
    {
        $old_pass = $request->get('old_pass', '');
        $new_pass = $request->get('new_pass', '');
        $re_new_pass = $request->get('re_new_pass', '');
        $user_id = Users::getUserId();
        $user = Users::where('id', $user_id)->first();
        if (!$user) {
            return $this->error("会员未找到");
        }
        if (!$old_pass) {
            return $this->error("请输入旧密码");
        }
        $old_pass_str = Users::MakePassword($old_pass);
        if ($user->password != $old_pass_str) {
            return $this->error("旧密码错误");
        }
        if ($new_pass != $re_new_pass) {
            return $this->error('两次密码不一致');
        }
        if (mb_strlen($new_pass) < 6 || mb_strlen($new_pass) > 16) {
            return $this->error('密码只能在6-16位之间');
        }
        $user->password = Users::MakePassword($new_pass);
        if ($user->save()) {
            return $this->success('成功');
        } else {
            return $this->error('失败');
        }

    }

    public function myInvite()
    {
        $data = ['rule' => [], 'sons' => []];
        $invite_rule = [];
        $invite = Setting::where('key', 'like', 'invite_%_%')->where('key', '<>', 'invite_code_must')->orderBy('value')->get();
        for ($i = 0; $i < 4; $i++) {
            foreach ($invite as $key => $value) {
                if ($value->key == ('invite_' . $i . '_min')) $invite_rule[$i]['min'] = $value->value;
                if ($value->key == ('invite_' . $i . '_max')) $invite_rule[$i]['max'] = $value->value;
                if ($value->key == ('invite_' . $i . '_price')) $invite_rule[$i]['price'] = $value->value;
            }
        }

        $user_id = Users::getUserId();
        $list =  Users::where("parent_id",$user_id)->where("is_charge",1)->select('id', 'time', 'first_consumption_time')->get();
        $data['rule'] = $invite_rule;
        $data['sons'] = $list;
        return $this->success($data);
    }

    public function rebate_flow(Request $request)
    {
        $user_id = Users::getUserId();
        $limit = $request->input('limit', 10);
        $lists = RebateFlow::where('receive_user_id', $user_id)->select('user_id', 'rebate_amount', 'create_time')
            ->orderBy('id', 'desc')
            ->paginate($limit);
        return $this->success($lists);
    }

    public function rebate_info(Request $request)
    {
        $user = Users::getById(Users::getUserId());
        if (!$user) {
            return $this->error("会员未找到");
        }
        $info = [
            "real_teamnumber" => $user->real_teamnumber,
            "candy_number" => $user->candy_number,
            "level" => Setting::get_level($user->real_teamnumber, $user->candy_number)
        ];
        $data["info"] = $info;
        $data["list"] = [];
        $list = Setting::where('key', 'like', '%level_rebate%')->get();
        if ($list) {
            $arr = [];
            foreach ($list as $val) {
                $arr[$val->key] = $val->value;
            }
            $list_arr = [];
            $arr_key = ["one", "two", "three"];
            foreach ($arr_key as $key => $v) {
                $c_arr = [];
                $c_arr["level"] = $key + 1;
                $c_arr["level_rebate_rate"] = $arr[$v . "_level_rebate_rate"] ?? 0.00;
                $c_arr["level_rebate_lower_num"] = $arr[$v . "_level_rebate_lower_num"] ?? 0;
                $c_arr["level_rebate_lock"] = $arr[$v . "_level_rebate_lock"] ?? 0.00;
                $list_arr[] = $c_arr;
            }
            $data["list"] = $list_arr;
        }

        return $this->success($data);
    }

    public function currery_info(Request $request)
    {
        $user = Users::getById(Users::getUserId());
        if (!$user) {
            return $this->error("会员未找到");
        }
        $info = [
            "real_teamnumber" => $user->real_teamnumber,
            "new_currery_number" => $user->new_currery_number,
            "rate" => Setting::get_currery($user->real_teamnumber, $user->new_currery_number)
        ];
        $data["info"] = $info;
        $data["list"] = [];
        $list = Setting::where('key', 'like', '%level_lottery%')->get();
        if ($list) {
            $arr = [];
            foreach ($list as $val) {
                $arr[$val->key] = $val->value;
            }
            $list_arr = [];
            $arr_key = ["one", "two", "three"];
            foreach ($arr_key as $key => $v) {
                $c_arr = [];
                $c_arr["level"] = $key + 1;
                $c_arr["level_lottery_rate"] = $arr[$v . "_level_lottery_rate"] ?? 0.00;
                $c_arr["level_lottery_lower_num"] = $arr[$v . "_level_lottery_lower_num"] ?? 0;
                $c_arr["level_lottery_currery"] = $arr[$v . "_level_lottery_currery"] ?? 00;
                $list_arr[] = $c_arr;
            }
            $data["list"] = $list_arr;
        }

        return $this->success($data);
    }

}

?>