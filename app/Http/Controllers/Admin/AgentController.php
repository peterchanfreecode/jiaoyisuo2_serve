<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agent;
use Illuminate\Http\Request;
use App\Models\AgentDomain;

class AgentController extends Controller
{
    public function index()
    {
        return view('admin.agent.index');
    }

    public function add(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            $result = new AgentDomain();
        } else {
            $result = AgentDomain::find($id);
        }
        $agent = Agent::where("level",1)->get();
        return view('admin.agent.add',["agent"=>$agent])->with('result', $result);
    }

    public function postAdd(Request $request)
    {
        $id = $request->get('id', 0);
        $agent_id = $request->get('agent_id', '');
        $agent_domain = $request->get('agent_domain', '');
        $agent_kefu = $request->get('agent_kefu', '');
        if (empty($id)) {
            $result = new AgentDomain();
        } else {
            $result = AgentDomain::find($id);
            if ($result == null) {
                return redirect()->back();
            }
        }
        $result->agent_id = $agent_id;
        $result->agent_domain = $agent_domain;
        $result->agent_kefu = $agent_kefu;
        try {
            $result->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $result = new AgentDomain();
        $result = $result->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }
    public function del(Request $request)
    {
        $id = $request->get('id', 0);
        $result = AgentDomain::find($id);
        if (empty($result)) {
            return $this->error('参数错误');
        }
        try {
            $result->delete();
            return $this->success('删除成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
