<?php

namespace App\Http\Controllers\Agen;

use App\Libraries\Step2auth\Step2AuthUtil;
use App\Traits\CheckGoogleSecurityCodeTraint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\{Agent, Setting, Users};

/**
 * 该类处理所有的代理商添加修改等操作
 * Class MemberController
 * @package App\Http\Controllers\Agent
 */
class MemberController extends Controller
{
    use CheckGoogleSecurityCodeTraint;

    private $agent_max_level = 4;

    function __construct()
    {
        $this->agent_max_level = Setting::getValueByKey('agent_max_level', 4);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {

            $username = $request->input("username", "");
            $password = $request->input("password", "");
            $google_code = $request->input('google_code', '');
            if (empty($username) || empty($password) || empty($google_code)) return $this->error("Parameter error");
            $agent = DB::table('agent')->where("username", $username)->where("status", 1)->first();
            if ($agent == null || empty($agent)) return $this->error("Agent not found");
            if ($agent->is_lock == 1) return $this->error("Account is locked, login prohibited");
            if ($username == "admin") {
                $key = Redis::get("google_key");
            } else {
                $key = Redis::get("google_agent_key" . $agent->id);
            }
            if (!$key) {
                return $this->error('Google captcha not set');
            }
            if ($this->checkSecurityCode($google_code, $key) == false) {
                return $this->error("Failed to verify security code");
            }
            if (Users::MakePassword($password) != $agent->password) {
                return $this->error("wrong password");
            }
            session()->put('agent_username', $agent->username);

            session()->put('agent_id', $agent->id);

            return $this->success('login successful！');
        } else {
            return $this->error('illegal operation！');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $res = Agent::delSession($request);
        if ($res) {
            return $this->success('exit successfully');
        } else {
            return $this->error('Logout failed, please try again');
        }
    }

    //修改密码页面
    public function setPas()
    {
        return view("agen.set.password");
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePWD(Request $request)
    {
        $post = $request->post();

        $oldPassword = $post['oldPassword'];
        $password = $post['password'];
        $repassword = $post['repassword'];
        $is_tong = $post['is_tong'];//是否同步用户密码
        $agent = Agent::getAgent();

        if (empty($agent)) {
            return $this->error('Agent does not exist');
        }
        if (mb_strlen($password) < 6 || mb_strlen($password) > 16) {
            return $this->error('The password can only be between 6-16 characters');
        }
        $now_password = $agent->getOriginal('password');

        $encrypted_password = Users::MakePassword($password);

        if (Users::MakePassword($oldPassword) != $now_password) {
            return $this->error("old password error");
        }

        if ($password !== $repassword) return $this->error('The two passwords do not match');
        if ($now_password == $encrypted_password) return $this->error('Can not be consistent with the original password');

        $agent->password = $encrypted_password;


        try {
            DB::beginTransaction();
            $agent->save();

            if ($is_tong == 1) {
                if ($agent->is_admin != 1) {
                    //同步用户密码
                    $user = Users::find($agent->user_id);
                    if ($user) {
                        $user->password = $encrypted_password;
                        $user->save();
                    }

                }
            }

            DB::commit();
            return $this->success('Successfully modified！');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

    //修改代理商基本信息
    public function setInfo()
    {
        $agent = Agent::getAgent();
        if (empty($agent)) {
            return $this->error('Agent does not exist');
        }
        // if($agent->is_admin == 1){
        //     abort(403, '超管无需修改');

        // }
        return view("agen.set.info", ['agent' => $agent]);
    }

    /**
     * 获取代理用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        $access_token = $request->get('access_token', 0);

        $agent = Agent::with('user')->where('id', session($access_token))->first();

        if (!$agent) return $this->error('llegal parameter！');

        return $this->ajaxReturn($agent);
    }


    public function saveUserInfo(Request $request)
    {
        $post = $request->post();


        $nickname = $post['nickname'];
        $phone = $post['phone'];
        $email = $post['email'];
        $agent = Agent::getAgent();
        if ($agent->is_admin == 1) {
            return $this->error('Super agent does not need to set');

        }
        $user_id = $agent->user_id;
        $user = Users::where('id', $user_id)->first();
        $user->nickname = $nickname;
        $user->phone = $phone;
        $user->email = $email;

        try {
            $user->save();
            return $this->success('Successfully modified！');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }


    }

    /**代理商列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function lists(Request $request)
    {

        $username = $request->input("username", "");
        $id = $request->input("id", 0);
        $user_id = $request->input("user_id", 0);
        $is_lock = $request->input("is_lock", 2);
        $is_addson = $request->input("is_addson", 2);
        $parent_agent_id = $request->input("parent_agent_id", 0);
        $limit = $request->get('limit', 10);
        $_self = Agent::getAgent();

        if ($_self === null) {
            return $this->outmsg('An error occurred! please login again');
        }

        $where = [];
        if (!empty($username)) {
            $where[] = ['username', '=', $username];

            $_search_us = Agent::getUserByUsername($username);

            if ($_search_us == null) {
                return $this->error('The agency does not exist');
            } else {
                $level_path_Arr = explode(',', $_search_us->agent_path);
                if (!in_array($_self->id, $level_path_Arr)) {
                    return $this->error('This agency is not part of your team');
                }
            }
        }
        if ($user_id) {
            $where[] = ['user_id', '=', $user_id];
        }
        if ($id > 0) {
            $where[] = ['id', '=', $id];

            $_search_us = Agent::getAgentById($id);
            if ($_search_us === null) {
                return $this->error('The agency does not exist');
            } else {
                $level_path_Arr = explode(',', $_search_us->agent_path);
                if (!in_array($_self->id, $level_path_Arr)) {
                    return $this->error('This agency is not part of your team');
                }
            }
        }
        if (in_array($is_lock, [0, 1])) {
            $where[] = ['is_lock', '=', $is_lock];
        }
        if (in_array($is_addson, [0, 1])) {
            $where[] = ['is_addson', '=', $is_addson];
        }

        if ($parent_agent_id > 0) {
            $where[] = ['parent_agent_id', '=', $parent_agent_id];
        } else {
            $where[] = ['parent_agent_id', '=', $_self->id];
        }


        $result = Agent::where('status', 1)->where($where)->paginate($limit);

        return $this->layuiData($result);
    }

    /**
     * 添加下级代理商时，查询该用户是否存在，是否已经是代理商等
     */
    public function searchuser(Request $request)
    {

        if ($request->isMethod('post')) {
            $user_id = $request->input("user_id", "");

            $_self = Agent::getAgent();

            if (!$_self) {
                return $this->outmsg('An error occurred! please login again');
            }

            if ($user_id) {

                $user = Users::getById($user_id);

                if ($user != null) {

                    $agent = Agent::getUserByUsername($user->account_number);
                    if ($agent === null) {
                        $agent_max_level = Setting::getValueByKey('agent_max_level', 4);
                        if (($_self->level == $agent_max_level && $_self->is_admin == 0)) {
                            return $this->notice("You are a level {$agent_max_level} agent and cannot add sub-agents");
                        } else if (($_self->is_addson == 0)) {
                            return $this->notice('You do not have permission to add sub-agents');
                        } else if (($_self->is_lock == 1)) {
                            return $this->notice('Your agency account is locked');
                        } else {
                            $returnData = [];
                            $returnData['user_id'] = $user->id;
                            $returnData['username'] = $user->account_number;
                            $returnData['son_level'] = 0;

                            if ($_self->level == 0 && $_self->is_admin == 1) {
                                $returnData['son_level'] = 1;
                            } else {
                                $returnData['son_level'] = $_self->level + 1;
                            }

                            $returnData['max_pro_loss'] = $_self->pro_loss;
                            $returnData['max_pro_ser'] = $_self->pro_ser;

                            return $this->ajaxReturn($returnData);
                        }
                    } else {
                        return $this->notice('The user is already an agent');
                    }
                } else {
                    return $this->error('this user does not exist');
                }
            } else {
                return $this->error('this user does not exist');
            }
        } else {
            return $this->error('illegal operation！');
        }
    }

    //验证下级代理商
    public function search_agent_son(Request $request)
    {

        if ($request->isMethod('post')) {
            $id = $request->input("id", 0);
            $user_id = $request->input("user_id", "");

            if ($id <= 0 || $user_id == '') {
                return $this->error('Parameter error');
            }

            $_self = Agent::getAgent();

            $_son = Agent::getAgentById($id);

            if (empty($_self) || empty($_son)) {
                return $this->outmsg('An error occurred! please login again');
            }
            $level_path_Arr = explode(',', $_son->agent_path);
            if (!in_array($_self->id, $level_path_Arr)) {
                return $this->error('This agency is not part of your team');
            }
            if (($_self->level == $this->agent_max_level && $_self->is_admin == 0)) {
                return $this->notice("You are a level {$this->agent_max_level} agent and cannot add sub-agents");
            } else if (($_self->is_addson == 0)) {
                return $this->notice('You do not have permission to add sub-agents');
            } else if (($_self->is_lock == 1)) {
                return $this->notice('Your agency account is locked');
            }
            if ($_son->level == $this->agent_max_level) {
                return $this->notice("The user is a level {$this->agent_max_level} agent and cannot add sub-agents");
            }

            if ($_son != null && !empty($id) && !empty($user_id) && $_self != null && !empty($_self)) {

                $user = Users::getById($user_id);
                if ($user != null) {

                    $agent = Agent::getUserByUsername($user->account_number);
                    if ($agent === null) {

                        $returnData = [];
                        $returnData['user_id'] = $user->id;
                        $returnData['username'] = $user->account_number;
                        $returnData['son_level'] = 0;

                        if ($_son->level == 0 && $_son->is_admin == 1) {
                            $returnData['son_level'] = 1;
                        } else {
                            $returnData['son_level'] = $_son->level + 1;
                        }

                        $returnData['max_pro_loss'] = $_son->pro_loss;
                        $returnData['max_pro_ser'] = $_son->pro_ser;

                        return $this->ajaxReturn($returnData);

                    } else {
                        return $this->notice('The user is already an agent');
                    }
                } else {
                    return $this->error('this user does not exist');
                }
            } else {
                return $this->error('this user does not exist');
            }
        } else {
            return $this->error('illegal operation！');
        }
    }

    /**
     * 添加下级的代理商
     * @param Request $request
     */
    public function addSonAgent(Request $request)
    {

        $_self = Agent::getAgent();


        $id = $request->input('agent_id', 0);//下级代理商id
        $_son = Agent::getAgentById($id);

        if ($_self === null) {
            return $this->outmsg('An error occurred! please login again');
        }

        if ($_son->level == $this->agent_max_level) {
            return $this->notice("The user is a level {$this->agent_max_level} agent and cannot add sub-agents");
        }

        if ($_self->level == $this->agent_max_level) {
            return $this->notice("you are a level {$this->agent_max_level} agent and cannot add sub-agents");

        } else if (($_self->is_addson == 0)) {
            return $this->notice('You do not have permission to add sub-agents');
        } else if (($_self->is_lock == 1)) {
            return $this->notice('Your agency account is locked');
        }

        //判断下级
        $username = $request->input('username', 0);
        $user_id = $request->input('user_id', 0);

        $id = $request->input('id', 0);
        if (DB::table('users')->where('account_number', $username)->where('id', $user_id)->first() === null) {
            return $this->error("this user does not exist! Please check user information again");
        }
        $ag = Agent::getUserByUsername($username);
        if ($ag !== null && $id == 0) {
            return $this->error("The user is already an agent！");
        }


        $rules = [
            /*    'pro_loss' => 'required|numeric|min:0.01|max:' . $_son->pro_loss,*/   //验证下级代理商的头寸比例是否正确
            /* 'pro_ser' => 'required|numeric|min:0.01|max:' . $_son->pro_ser,*/ // //验证下级代理商的手续费比例是否正确
            'is_lock' => 'required|in:1,0',
            'is_addson' => 'required|in:1,0',
            'user_id' => 'required|integer|min:0',
            'id' => 'required|integer|min:0'
        ];

        $messages = [
            'is_lock.required' => 'Whether to lock or not can not be empty',
            'is_lock.in' => 'Whether to lock the parameter error',
            'is_addson.required' => 'Whether to fill in the new can not be empty',
            'is_addson.in' => 'Is it wrong to fill in the new parameters?',
            'user_id.required' => 'Is it wrong to fill in the new parameters?',
            'user_id.integer' => 'Is it wrong to fill in the new parameters?',
            'user_id.min' => 'illegal operation',
            'id.required' => 'Is it wrong to fill in the new parameters?',
            'id.integer' => 'Is it wrong to fill in the new parameters?',
            'id.min' => 'illegal operation'
        ];

        //创建验证器
        $validator = Validator::make($request->all(), $rules, $messages);
        //以上验证通过后 继续验证 .  测试用的～ ：）
        $validator->after(function ($validator) use ($request) {
            $user = Users::getById($request->get('user_id'));
            if (empty($user)) {
                return $validator->errors()->add('isUser', 'no such user');
            }
        });

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $user = Users::getById($request->get('user_id'));
        //判断添加下级的代理商的等级
        if ($_son->level == 0 && $_son->is_admin == 1) {
            $level = 1;
        } else {
            $level = $_son->level + 1;
        }

        if ($id > 0) {
            $agent = Agent::find($id);
        } else {
            $agent = new Agent();
            $agent->reg_time = time();
        }
        $agent->user_id = $user_id;
        $agent->username = $username;
        $agent->password = $user->password;
        $agent->parent_agent_id = $_son->id;  //上级代理商id，有别于user表中的parent_id。  这个id取的是agent产生的id,并不是users表中的id。特别要注意！
        $agent->level = $level;
        $agent->is_admin = 0;
        $agent->is_lock = $request->input('is_lock', 0);
        $agent->is_addson = $request->input('is_addson', 1);
        $agent->pro_loss = $request->input('pro_loss', 0.00);
        $agent->pro_ser = $request->input('pro_ser', 0.00);
        $agent->status = 1;

        try {
            DB::beginTransaction();
            if (!$agent->save()) {
                DB::rollBack();
                return $this->error("operation failed! Please try again");
            }
            if ($_son->is_admin == 1) {
                $agent->agent_path = $agent->id . ',' . $_son->id;
            } else {
                $agent->agent_path = $agent->id . ',' . $_son->agent_path; //上级代理商id的字符串拼接，这个id取的是agent产生的id,并不是users表中的id。特别要注意！
            }
            if ($agent->save()) {

                //更新该用户的代理商id
                $_users = Users::lockForUpdate()->find($user_id);
                $_users->agent_path = $agent->agent_path;
                $_users->agent_note_id = $_son->id;
                $_users->agent_id = $agent->id;
                $_users->save();
                if ($id > 0) {
                    DB::commit();
                    return $this->success("Successful operation");
                } else {
                    $key = "google_agent_key" . $agent->id;
                    $ga = new Step2AuthUtil();
                    $step2SecretKey = $ga->createSecret();
                    $googleVerifyQrCodeUrl = $ga->getQRCodeGoogleUrl(urlencode("agent_" . $agent->id), $step2SecretKey);
                    $msg = "<br/><span style='color: red'>proxy id:{$agent->id}</span>";
                    $msg .= "<br/><img src='{$googleVerifyQrCodeUrl}'/>";
                    $msg .= "<br/><br/><span color='red'>" . $step2SecretKey . "</span>";
                    if (Redis::set($key, $step2SecretKey)) {
                        DB::commit();
                        return $this->ajaxReturn(["msg" => $msg], "Successful operation", "300");
                    } else {
                        DB::rollBack();
                        return $this->error("Setup failed");
                    }
                }
            } else {
                DB::rollBack();
                return $this->error("operation failed! Please try again");
            }
        } catch (\Exception $ex) {                  //\Exception 捕获所有异常
            DB::rollBack();
            return $this->error($ex->getMessage()); // getMessage() 异常信息
        }
    }


    /**
     * 添加 编辑代理商
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAgent(Request $request)
    {

        $_self = Agent::getAgent();

        if ($_self === null) {
            return $this->outmsg('An error occurred! please login again');
        }
        //判断下级
        $username = $request->input('username', 0);
        $user_id = $request->input('user_id', 0);

        $id = $request->input('id', 0);//编辑
        if (DB::table('users')->where('account_number', $username)->where('id', $user_id)->first() === null) {
            return $this->error("this user does not exist! Please check user information again");
        }
        $ag = Agent::getUserByUsername($username);
        if ($ag !== null && $id == 0) {
            return $this->error("The user is already an agent！");
        }

        //判断自己
        if (($_self->level == $this->agent_max_level && $_self->is_admin == 0 && $id == 0)) {
            return $this->notice("You are a level {$this->agent_max_level} agent and cannot add sub-agents");
        } else if (($_self->is_addson == 0)) {
            return $this->notice('You do not have permission to add sub-agents');
        } else if (($_self->is_lock == 1)) {
            return $this->notice('Your agency account is locked');
        }
        $rules = [
            'is_lock' => 'required|in:1,0',
            'is_addson' => 'required|in:1,0',
            'user_id' => 'required|integer|min:0',
            'id' => 'required|integer|min:0'
        ];

        $messages = [
            'is_lock.required' => 'Whether to lock or not can not be empty',
            'is_lock.in' => 'Whether to lock the parameter error',
            'is_addson.required' => 'Whether to fill in the new can not be empty',
            'is_addson.in' => 'Is it wrong to fill in the new parameters?',
            'user_id.required' => 'Is it wrong to fill in the new parameters?',
            'user_id.integer' => 'Is it wrong to fill in the new parameters?',
            'user_id.min' => 'illegal operation',
            'id.required' => 'Is it wrong to fill in the new parameters?',
            'id.integer' => 'Is it wrong to fill in the new parameters?',
            'id.min' => 'illegal operation'
        ];

        //创建验证器
        $validator = Validator::make($request->all(), $rules, $messages);
        //以上验证通过后 继续验证 .  测试用的～ ：）
        $validator->after(function ($validator) use ($request) {
            $user = Users::getById($request->get('user_id'));
            if (empty($user)) {
                return $validator->errors()->add('isUser', 'no such user');
            }
        });

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $user = Users::getById($request->get('user_id'));
        if ($id > 0) {
            $agent = Agent::find($id);
            //编辑下一级代理商密码
            if (($agent->parent_agent_id == $_self->id) || ($_self->level == 0)) {
                $agent_password = $request->input('agent_password', '');
                if ($agent_password) {
                    $agent->password = Users::MakePassword($agent_password);
                }
            }

        } else {
            $agent = new Agent();
            $agent->reg_time = time();
            $agent->parent_agent_id = $_self->id;  //上级代理商id，有别于user表中的parent_id。  这个id取的是agent产生的id,并不是users表中的id。特别要注意！

            //判断添加下级的代理商的等级
            if ($_self->level == 0 && $_self->is_admin == 1) {
                $level = 1;
            } else {
                $level = $_self->level + 1;
            }
            $agent->level = $level;
            $agent->password = $user->password;
        }

        $agent->user_id = $user_id;
        $agent->username = $username;

        $agent->is_admin = 0;
        $agent->is_lock = $request->input('is_lock', 0);
        $agent->is_addson = $request->input('is_addson', 1);
        $agent->pro_loss = $request->input('pro_loss', 0.00);
        $agent->pro_ser = $request->input('pro_ser', 0.00);
        $agent->status = 1;

        try {
            DB::beginTransaction();
            if (!$agent->save()) {
                DB::rollBack();
                return $this->error("operation failed! Please try again");
            }
            if ($_self->is_admin == 1) {
                $agent->agent_path = $agent->id . ',' . $_self->id;
            } else {
                $agent->agent_path = $agent->id . ',' . $_self->agent_path;
            }
            if ($agent->save()) {
                $_users = Users::lockForUpdate()->find($user_id);
                $_users->agent_path = $agent->agent_path;
                $_users->agent_note_id = $_self->id;
                $_users->agent_id = $agent->id;
                $_users->save();

                if ($id > 0) {
                    DB::commit();
                    return $this->success("Successful operation");
                } else {
                    $key = "google_agent_key" . $agent->id;
                    $ga = new Step2AuthUtil();
                    $step2SecretKey = $ga->createSecret();
                    $googleVerifyQrCodeUrl = $ga->getQRCodeGoogleUrl(urlencode("agent_" . $agent->id), $step2SecretKey);
                    $msg = "<br/><span style='color: red'>代理id:{$agent->id}</span>";
                    $msg .= "<br/><img src='{$googleVerifyQrCodeUrl}'/>";
                    $msg .= "<br/><br/><span color='red'>" . $step2SecretKey . "</span>";
                    if (Redis::set($key, $step2SecretKey)) {
                        DB::commit();
                        return $this->ajaxReturn(["msg" => $msg], "Successful operation", "300");
                    } else {
                        DB::rollBack();
                        return $this->error("Setup failed");
                    }
                }

            } else {
                DB::rollBack();
                return $this->error("operation failed! Please try again");
            }
        } catch (\Exception $ex) {
            DB::rollBack();                 //\Exception 捕获所有异常
            return $this->error($ex->getMessage()); // getMessage() 异常信息
        }
    }

    public function updateAgent(Request $request)
    {
        //判断下级
        $agentid = $request->input('agentid', 0);
        $_h = Agent::getAgentById($agentid);
        if ($_h == null || $_h->id <= 0) {
            return $this->error("this user does not exist! Please check user information again");
        }

        $rules = [
            'agentid' => 'required|numeric|min:1|max:999999999',   //id必须是数字
            'name' => 'required|in:is_lock,is_addson', //必须是指定的字段
            'value' => 'required|in:1,0'   //必须是指定的值
        ];

        $messages = [
            'agentid.required' => 'User id cannot be empty',
            'agentid.numeric' => 'User id can only be numbers',
            'agentid.min' => 'The minimum user id is 1',
            'agentid.max' => 'The maximum value of user id is 999999999',
            'name.required' => 'Modify attribute cannot be empty',
            'name.in' => 'Modify attribute parameter error',
            'value.required' => 'Modify attribute cannot be empty',
            'value.in' => 'Modify attribute parameter error'
        ];

        //创建验证器
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $agent = new Agent();
        $name = $request->input('name', 0);
        $value = $request->input('value', 0);

        if ($name == 'is_lock' && $value == 1) {
            $lock = time();
        } else {
            $lock = 0;
        }

        $res = $agent->where('id', $agentid)->update([$name => $value, 'lock_time' => $lock]);

        if ($res) {
            return $this->success('update completed');
        } else {
            return $this->error('Update failed, please try again');
        }
    }


    /**
     * @param $san_user
     *
     */
    public function sel_arr($arr = array())
    {
        if (!empty($arr)) {
            $new_arr = [];
            foreach ($arr as $k => $val) {
                $new_arr[] = $val->user_id;
            }
            return $new_arr;
        } else {
            return [];
        }
    }

    /**
     * @param $san_user
     *
     */
    public function sel_agent_arr($arr = array())
    {
        if (!empty($arr)) {
            $new_arr = [];
            foreach ($arr as $k => $val) {
                $new_arr[] = $val->id;
            }
            return $new_arr;
        } else {
            return [];
        }
    }

    public function allChildAgent()
    {
        $agents = Agent::getAllChildAgent(Agent::getAgentId());
        return $this->ajaxReturn($agents);
    }

    public function del_agent()
    {

        $agent_id = request()->get('id', 0);

        $agent_info = Agent::getAgentById($agent_id);
        if (!$agent_info) {
            return $this->error("agent does not exist");
        }
        $users = Users::whereRaw("FIND_IN_SET($agent_id,agent_path)")->where("agent_id", "!=", $agent_id)->get()->toArray();
        if ($users) {
            return $this->error("There are still members under the agent name, please do not delete");
        }
        $Agent = Agent::whereRaw("FIND_IN_SET($agent_id,agent_path)")->where("id", "!=", $agent_id)->where("status", 1)->get()->toArray();
        if ($Agent) {
            return $this->error("There are subordinate agents under this agent name, please do not delete");
        }
        try {
            //code...
            $agent_info->status = -1;
            $agent_info->save();
            $info_user = Users::where("id", $agent_info["user_id"])->first();
            $info_user->agent_id = 0;
            $info_user->save();
            return $this->success("Successful operation");

        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage());
        }
    }

    public function google_code()
    {
        $agent_id = request()->get('id', 0);
        $agent_info = Agent::getAgentById($agent_id);
        if (!$agent_info) {
            return $this->error("agent does not exist");
        }
        $key = "google_agent_key" . $agent_id;
        $ga = new Step2AuthUtil();
        $step2SecretKey = $ga->createSecret();
        $googleVerifyQrCodeUrl = $ga->getQRCodeGoogleUrl(urlencode("agent_" . $agent_id), $step2SecretKey);
        $msg = "<br/><span style='color: red'>proxy id:{$agent_id}</span>";
        $msg .= "<br/><img src='{$googleVerifyQrCodeUrl}'/>";
        $msg .= "<br/><br/><span color='red'>" . $step2SecretKey . "</span>";
        if (Redis::set($key, $step2SecretKey)) {
            return $this->ajaxReturn(["msg" => $msg], "Successful operation", "300");
        } else {
            return $this->error("Setup failed");
        }
    }

}
