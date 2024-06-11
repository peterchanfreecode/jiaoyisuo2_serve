<?php

/**
 * Created by PhpStorm.
 * User: YSX
 * Date: 2018/12/4
 * Time: 16:36
 */

namespace App\Http\Controllers\Agen;

use Illuminate\Http\Request;
use App\Models\{AccountLog, Agent, Currency, MicroSecond, Token, UserMicro, Users, UsersWalletOut};
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    //用户管理
    public function index()
    {
        //某代理商下用户时
        $parent_id = request()->get('parent_id', 0);
        //币币  
        $legal_currencies = Currency::get();
        $self = Agent::getAgent()->toArray();
        return view("agen.user.index", ["is_admin" => $self["is_admin"], 'parent_id' => $parent_id, 'legal_currencies' => $legal_currencies]);
    }

    //用户列表
    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $id = request()->input('id', 0);
        $parent_id = request()->input('parent_id', 0);
        $account_number = request()->input('account_number', '');
        $start = request()->input('start', '');
        $end = request()->input('end', '');
        $users = new Users();

        $users = $users->leftjoin("user_real", "users.id", "=", "user_real.user_id");

        if ($id) {
            $users = $users->where('users.id', $id);
        }
        if ($parent_id > 0) {
            $users = $users->where('users.agent_note_id', $parent_id);
        }
        if ($account_number) {
            $users = $users->where('users.account_number', $account_number);
        }
        if (!empty($start) && !empty($end)) {
            $users->whereBetween('users.time', [strtotime($start . ' 0:0:0'), strtotime($end . ' 23:59:59')]);
        }

        $agent_id = Agent::getAgentId();
        $users = $users->whereRaw("FIND_IN_SET($agent_id,users.agent_path)");
        $list = $users->select("users.*", "user_real.card_id")->paginate($limit);
        $list = Users::get_en_lists($list);
        return $this->layuiData($list);
    }

    /**
     * 获取用户管理的统计
     * @param Request $r
     */
    public function get_user_num(Request $request)
    {

        $id = request()->input('id', 0);
        $account_number = request()->input('account_number', '');
        $parent_id = request()->input('parent_id', 0);//代理商id
        $start = request()->input('start', '');
        $end = request()->input('end', '');
        $currency_id = request()->input('currency_id', '');
        $users = new Users();
        if ($id) {
            $users = $users->where('id', $id);
        }
        if ($parent_id > 0) {
            $users = $users->where('agent_note_id', $parent_id);
        }
        if ($account_number) {
            $users = $users->where('account_number', $account_number);
        }
        if (!empty($start) && !empty($end)) {
            $users->whereBetween('time', [strtotime($start . ' 0:0:0'), strtotime($end . ' 23:59:59')]);
        }

        $agent_id = Agent::getAgentId();
        $users = $users->whereRaw("FIND_IN_SET($agent_id,`agent_path`)");
        $users_id = $users->get()->pluck('id')->all();
        $_daili = 0;
        $_ru = 0.00;
        $_chu = 0.00;
        $_num = 0;

        $_num = $users->count();

        $_daili = $users->where('agent_id', '>', 0)->count();


        $_ru = AccountLog::where('type', AccountLog::CHANGEREQ)
            ->whereIn('user_id', $users_id)
            ->when($currency_id > 0, function ($query) use ($currency_id) {
                $query->where('currency', $currency_id);
            })->sum('value');

        $_chu = UsersWalletOut::where('status', 2)
            ->whereIn('user_id', $users_id)
            ->when($currency_id > 0, function ($query) use ($currency_id) {
                $query->where('currency', $currency_id);
            })->sum('real_number');

        $data = [];
        $data['_num'] = $_num;
        $data['_daili'] = $_daili;
        $data['_ru'] = $_ru;
        $data['_chu'] = $_chu;


        return $this->ajaxReturn($data);
    }

    //我的邀请二维码
    public function get_my_invite_code()
    {

        $_self = Agent::getAgent();

        if ($_self == null) {
            $this->outmsg('超时');
        }

        $use = Users::getById($_self->user_id);

        return $this->ajaxReturn(['invite_code' => $use->extension_code, 'is_admin' => $_self->is_admin]);
    }

    //代理商管理
    public function salesmenIndex()
    {
        $self = Agent::getAgent()->toArray();
        return view("agen.salesmen.index", ["is_admin" => $self["is_admin"], "id" => $self["id"]]);
    }

    //添加代理商页面
    public function salesmenAdd()
    {
        $data = request()->all();

        return view("agen.salesmen.add", ['d' => $data]);
    }

    //添加代理商页面
    public function salesmenAddress()
    {
        $data = request()->all();

        return view("agen.salesmen.address", ['d' => $data]);
    }

    public function salesmenAddressSave()
    {
        $data = request()->all();

        $agent_id = Agent::getAgentId();

        if ($agent_id == 1) {

            $agent = Agent::find($data['id']);
            $agent->btc_address = $data['btc_address'];
            $agent->usdt_address = $data['usdt_address'];
            $agent->save();
        }

        return $this->success("操作成功");
    }


    public function salesmenEdit()
    {
        $data = request()->all();
        return view("agen.salesmen.add", ['d' => $data]);
    }

    public function update_agent()
    {
        $data = request()->all();
        $leval = $data["level"];
        $list = Agent::where("level", $leval - 1)->where("status", 1)->where("id", "!=", $data["parent_agent_id"])->get();
        return view("agen.salesmen.update_agent", ['d' => $list, "id" => $data["id"]]);
    }

    public function update_agent_add(Request $request)
    {
        //判断下级
        $agent_id = $request->input('agent_id', 0);
        $id = $request->input('id', 0);//编辑
        $info = Agent::getAgent();
        if (!$info) {
            return $this->error("please login again");
        }
        if ($info["level"] != 0) {
            return $this->error("Not authorized for this operation");
        }
        if (!$agent_id) {
            return $this->error("Please select an agent");
        }
        if (!$id) {
            return $this->error("Parameter error");
        }
        $agent_info = Agent::getAgentById($agent_id);
        if (!$agent_info) {
            return $this->error("The selected proxy does not exist");
        }
        $info = Agent::getAgentById($id);
        if (!$info) {
            return $this->error("The proxy for the operation does not exist");
        }
        $old_parent = $info->parent_agent_id;

        try {
            DB::beginTransaction();
            $info->agent_path = Agent::update_agentPath($info->agent_path, $info->parent_agent_id, $agent_id);
            $info->parent_agent_id = $agent_id;
            $info->save();
            Users::where('id', $info["user_id"])->update(["agent_note_id" => $agent_id, "agent_path" => $info->agent_path]);
            //查出所有该代理的用户
            $list = Users::whereRaw("FIND_IN_SET($id,users.agent_path)")->get();

            if ($list) {
                foreach ($list as $v) {
                    $v->agent_path = Agent::update_agentPath($v->agent_path, $old_parent, $agent_id);
                    $v->save();
                }
            }
            DB::commit();
            return $this->success("Successful operation");
        } catch (\Exception $ex) {                  //\Exception 捕获所有异常
            DB::rollBack();
            return $this->error($ex->getMessage()); // getMessage() 异常信息
        }
    }

    //出入金管理
    public function transferIndex()
    {
        return view("agen.user.transfer");
    }

    //用户点控
    public function risk()
    {

        $user_id = request()->get('id', 0);
        $user = Users::find($user_id);
        return view("agen.user.risk", ['result' => $user]);
    }

    public function postRisk()
    {

        $user_id = request()->get('id', 0);
        $risk = request()->get('risk', 0);
        $user = Users::find($user_id);
        $agent_id = Agent::getAgentId();
        $parent_agent = explode(',', $user->agent_path);

        if (!in_array($agent_id, $parent_agent)) {
            return $this->error('Not a user under your umbrella, inoperable');
        }
        try {
            //code...
            $user->risk = $risk;
            $user->save();
            return $this->success("Successful operation");

        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage());
        }
    }

    public function update_user_agent()
    {

        $user_id = request()->get('id', 0);
        $agent_id = request()->get('agent_id', 0);
        $user = Users::find($user_id);
        if ($user->agent_id > 0) {
            return $this->error("该会员有代理账户,不能更换代理");
        }
        $agent_info = Agent::getAgentById($agent_id);
        if (!$agent_info) {
            return $this->error("更换的代理不存在");
        }

        try {
            //code...
            $user->agent_note_id = $agent_id;
            $user->agent_path = $agent_info["agent_path"];
            $user->save();
            return $this->success("操作成功");

        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage());
        }
    }
    public function doLock(Request $request)
    {
        $id = $request->get('id', 0);
        $status = $request->get('status', 0);

        if (empty($id)) {
            return $this->error('Parameter error');
        }
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('Parameter error');
        }

        $user->status = $status;
        if (!$user->save()) {
            return $this->error('lock failed');
        }
        if($status == 1){
            Token::clearToken($id);
        }
        return $this->success('Successful operation');
    }
    public function do_update_password(Request $request)
    {
        $id = $request->get('id', 0);
        $password = $request->get('new_password', '');
        $re_password = $request->get('confirm_new_password', '');
        if ($password != $re_password) {
            return $this->error('The two passwords do not match');
        }
        if (mb_strlen($password) < 6 || mb_strlen($password) > 16) {
            return $this->error('Password can only be between 6-16 characters');
        }
        if (empty($id)) {
            return $this->error('Parameter error');
        }
        $user = Users::find($id);
        if (empty($user)) {
            return $this->error('Parameter error');
        }
        $user->password = Users::MakePassword($password);
        if (!$user->save()) {
            return $this->error('fail to edit');
        }
        Token::clearToken($id);
        return $this->success('Successfully modified');
    }

    public function update_password()
    {
        $user_id = request()->get('id', 0);
        return view("agen.user.modify_password", ['user_id' => $user_id]);
    }

    public function user_mic(Request $request)
    {
        $user_id = request()->get('id', 0);
        $list = MicroSecond::where("status", 1)->get();
        $user_mic_list = UserMicro::where("user_id", $user_id)->get();
        $user_mic_data = [];
        if ($user_mic_list) {
            foreach ($user_mic_list as $v) {
                $user_mic_data[$v["mic_sec_id"]]["profit_ratio"] = $v["profit_ratio"];
            }
        }
        if ($list) {
            foreach ($list as &$v) {
                $v["profit_ratio"] = $user_mic_data[$v["id"]]["profit_ratio"] ?? $v["profit_ratio"];
            }
        }
        return view("agen.user.mic", ['list' => $list, "user_id" => $user_id]);
    }

    public function postMicro(Request $request)
    {
        $user_id = request()->get('user_id', 0);
        $data = request()->all();
        unset($data["user_id"]);
        unset($data["s"]);
        try {
            DB::beginTransaction();
            foreach ($data["mic_sec_id"] as $k => $v) {
                $info = UserMicro::where("user_id", $user_id)->where("mic_sec_id", $v)->first();

                if ($info) {
                    $info->profit_ratio = $data["profit_ratio"][$k] ?? 0.00;
                    $info->lose_ratio = $data["lose_ratio"][$k] ?? 0.00;
                    $info->save();
                } else {
                    $model = new UserMicro();
                    $model->user_id = $user_id;
                    $model->mic_sec_id = $v;
                    $model->profit_ratio = $data["profit_ratio"][$k] ?? 0.00;
                    $model->lose_ratio = $data["lose_ratio"][$k] ?? 0.00;
                    $model->save();
                }
            }
            DB::commit();
            return $this->success('Successfully modified');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }

    }

}
