<?php

namespace App\Http\Controllers\Admin;
// use Symfony\Component\HttpFoundation\Request;
use Illuminate\Http\Request;

use App\Models\Users;
use App\Models\UserReal;
use App\Events\RealNameEvent;
use Illuminate\Support\Facades\DB;

class UserRealController extends Controller
{

    public function index()
    {
        return view("admin.userReal.index");
    }

    //用户列表
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        $review_status_s = $request->get('review_status_s', -1);
        $user_id = $request->get('user_id', '');

        $list = new UserReal();
        if (!empty($account)) {
            $list = $list->whereHas('user', function ($query) use ($account) {
                $query->where("phone", 'like', '%' . $account . '%')->orwhere('email', 'like', '%' . $account . '%');
            });
        }
        if($review_status_s != -1){
            $list = $list->where('status',$review_status_s);
        }
        if($user_id){
            $list = $list->where('user_id',$user_id);
        }
        $list = $list->orderBy('id', 'desc')->paginate($limit);
        if($list){
            foreach($list as $v){
                if (!stristr($v["front_pic"], 'http')) {
                    $v["front_pic"] =  config('app.aws_url') . $v["front_pic"];
                }
                if (!stristr($v["reverse_pic"], 'http')) {
                    $v["reverse_pic"] = config('app.aws_url') . $v["reverse_pic"];
                }
            }
        }
        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);
    }

    public function detail(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->post('id', 0);
            $card_id = $request->post('card_id', 0);
            $name = $request->post('uname', 0);
            DB::table('user_real')->where('id', $id)->update([
                'card_id' => $card_id,
                'name' => $name,
            ]);
            return $this->success('修改成功');
        }

        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error("参数错误");
        }

        $result = UserReal::find($id);

        return view('admin.userReal.info', ['result' => $result]);
    }

    public function del(Request $request)
    {
        $id = $request->get('id');
        $userreal = UserReal::find($id);
        if (empty($userreal)) {
            $this->error("认证信息未找到");
        }
        try {

            $userreal->delete();
            return $this->success('删除成功');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    public function auth(Request $request)
    {
        $id = $request->get('id', 0);
        $action = $request->get('action', 0);
        $userreal = UserReal::find($id);
        if (empty($userreal)) {
            return $this->error('参数错误');
        }
        $user = Users::find($userreal->user_id);
        if (!$user) {
            return $this->error('用户不存在');
        }
        if($action =="suc"){
            $arr = [
                'auth_low' => 2,
                'is_realname' => 2
            ];
            Users::where('id', $userreal->user_id)->update($arr);
            $userreal->status = 2;
            $userreal->save();

        }else if($action =="fail"){
            $arr = [
                'auth_low' => 3,
            ];
            Users::where('id', $userreal->user_id)->update($arr);
            $userreal->status = 1;
            $userreal->save();
        }
        return $this->success('操作成功');
        /*if ($userreal->review_status == 1 || $userreal->review_status == 0) {
            //从未认证到认证
            //查询users表判断是否为第一次实名认证
            $is_realname = $user->is_realname;
            if ($is_realname != 2) {

                //判断自己上级的的触发奖励
                //UserDAO::addCandyNumber($user);
            }
            $userreal->review_status = 2;
        } else if ($userreal->review_status == 2) {
            Users::where('id', $userreal->user_id)->update([
                'auth_low' => 0,
                'auth_high' => 0,
                'is_realname' => 1
            ]);
            $userreal->review_status = 1;
        } else {
            $userreal->review_status = 1;
        }
        try {
            $userreal->save();
            //用户实名事件
            if ($userreal->review_status == 2) {
                event(new RealNameEvent($user, $userreal));
            }
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }*/
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
