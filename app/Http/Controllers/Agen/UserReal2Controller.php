<?php

namespace App\Http\Controllers\Agen;

use App\Models\Agent;
use Symfony\Component\HttpFoundation\Request;
use App\Models\Users;
use App\Models\UserRealHigh;

class UserReal2Controller extends Controller
{

    public function index()
    {
        $self = Agent::getAgent()->toArray();
        return view("agen.userReal2.index",["level" => $self["level"]]);
    }

    //用户列表
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $agent_id = Agent::getAgentId();
        $node_users = Users::whereRaw("FIND_IN_SET($agent_id,`agent_path`)")->pluck('id')->all();
        $lists = UserRealHigh::whereIn('user_id', $node_users)
            ->where(function ($query) use ($request) {
                $account = $request->get('account', '');
                $review_status_s = $request->get('review_status_s', -1);
                $user_id = $request->get('user_id', '');
                $query->when($account != '', function ($query) use ($account) {
                    $query->whereHas('user', function ($query) use ($account) {
                        $query->where('account_number', $account);
                    });
                })->when($review_status_s != -1, function ($query) use ($review_status_s) {
                    $query->where('status', $review_status_s);
                })->when($user_id > 0, function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
            })->orderBy('id', 'desc')
            ->paginate($limit);
        $items = $lists->getCollection();
        $lists->setCollection($items);
        if ($lists) {
            foreach ($lists as $v) {
                if (!stristr($v["hand_pic"], 'http')) {
                    $v["hand_pic"] = config('app.aws_url') . $v["hand_pic"];
                }
            }

        }
        return $this->layuiData($lists);
    }

    public function detail(Request $request)
    {

        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error("Parameter error");
        }

        $result = UserRealHigh::find($id);

        return view('admin.userReal2.info', ['result' => $result]);
    }

    public function del(Request $request)
    {
        $id = $request->get('id');
        $userreal = UserRealHigh::find($id);
        if (empty($userreal)) {
            $this->error("Authentication information not found");
        }
        try {

            $userreal->delete();
            return $this->success('successfully deleted');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    public function auth(Request $request)
    {
        $id = $request->get('id', 0);
        $action = $request->get('action', 0);
        $userreal = UserRealHigh::find($id);
        if (empty($userreal)) {
            return $this->error('Parameter error');
        }
        $user = Users::find($userreal->user_id);
        if (!$user) {
            return $this->error('User does not exist');
        }
        if ($user['auth_low'] != 2) {
            return $this->error('Primary certification is required');
        }
        if ($action == "suc") {
            $arr = [
                'auth_high' => 2,
                'is_realname' => 2
            ];
            Users::where('id', $userreal->user_id)->update($arr);
            $userreal->status = 2;
            $userreal->save();

        } else if ($action == "fail") {
            $arr = [
                'auth_high' => 3,
            ];
            Users::where('id', $userreal->user_id)->update($arr);
            $userreal->status = 1;
            $userreal->save();
        }
        return $this->success('Successful operation');

    }

    //递归查询用户下级所有人数
    public function GetTeamMember($members, $mid)
    {
        $Teams = array();//最终结果
        $mids = array($mid);//第一次执行时候的用户id
        do {
            $othermids = array();
            $state = false;
            foreach ($mids as $valueone) {
                foreach ($members as $key => $valuetwo) {
                    if ($valuetwo['parent_id'] == $valueone) {
                        //实名认证通过的团队人数
                        $Teams[] = $valuetwo['id'];//找到我的下级立即添加到最终结果中
                        $othermids[] = $valuetwo['id'];//将我的下级id保存起来用来下轮循环他的下级
                        //                        array_splice($members,$key,1);//从所有会员中删除他
                        $state = true;
                    }
                }
            }
            $mids = $othermids;//foreach中找到的我的下级集合,用来下次循环
        } while ($state == true);
        //$Teams=Users::where("parents_path","like","%$mid%")->where("is_realname","=",2)->count();
        $Teams = Users::whereIn("id", $Teams)->where("is_realname", "=", 2)->count();
        return $Teams;
    }
}
