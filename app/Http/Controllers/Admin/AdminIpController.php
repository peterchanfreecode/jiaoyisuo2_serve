<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminIp;
use Illuminate\Support\Facades\DB;

class AdminIpController extends Controller
{
    public function index()
    {
        return view('admin.admin_ip.index');
    }

    public function add()
    {
        return view('admin.admin_ip.add');
    }

    public function postAdd(Request $request)
    {
        $ip = $request->get('ip', '');
        if (empty($ip)) {
            return $this->error("参数错误");
        }
        $info = AdminIp::where("ip", $ip)->first();
        if ($info) {
            return $this->error("白名单已存在,请勿重复添加");
        }

        try {
            DB::beginTransaction();
            $model = new AdminIp();
            $model->ip = $ip;
            $model->save();
            DB::commit();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $ip = $request->get('ip', "");
        $list = new AdminIp();
        if ($ip) {
            $list = $list->where('ip', $ip);
        }
        $result = $list->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }

    public function del(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error("参数错误");
        }
        $info = AdminIp::find($id);
        try {
            DB::beginTransaction();
            $info->delete();
            DB::commit();
            return $this->success('删除成功');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

}
