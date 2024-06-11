<?php

namespace App\Http\Controllers\Agent;
use Illuminate\Http\Request;
use App\Models\AgentReport;
use App\Models\Agent;
class AgentReportController extends Controller
{
    public function index()
    {
        return view('agent.agent_report.index');
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $start = $request->input("start_time", '');
        $end = $request->input("end_time", '');
        $user_id = $request->input("user_id", '');
        $result = new AgentReport();
        $id = Agent::getAgentId();
        $ids = Agent::whereRaw("FIND_IN_SET($id,agent_path)")->pluck("id")->all();

        $result = $result->wherein("agent_id",$ids)
                ->when($start !='', function ($query) use ($start) {
                    $query->where('create_time','>=', $start);
                })->when($user_id !='', function ($query) use ($user_id) {
                        $query->whereHas('agent', function ($query) use ($user_id) {
                            $query->where('user_id',$user_id);
                        });
                })->when($end !='', function ($query) use ($end) {
                    $query->where('create_time','<=', $end);
                })->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }
}
