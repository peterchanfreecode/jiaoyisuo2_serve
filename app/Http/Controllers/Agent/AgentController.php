<?php
/**
 * Created by PhpStorm.
 * User: YSX
 * Date: 2018/12/4
 * Time: 19:08
 */

namespace App\Http\Controllers\Agent;


use App\Models\Agent;
use App\Models\Users;

class AgentController extends Controller
{

    /**代理商信息
     * 可以传用户id也可以传代理商id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function info()
    {
        $agent_id = request('agent_id', 0);
        if (!$agent_id ) {
            return $this->error('参数错误');
        }
        $agent = new Agent();
        if ($agent_id) {
            $agent = $agent->where('id', $agent_id);
        }
        $agent = $agent->first();
        return $this->success($agent);
    }

}