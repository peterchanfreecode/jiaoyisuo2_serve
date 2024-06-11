<?php

namespace App\Http\Controllers\Admin;

use App\Models\Users;
use Symfony\Component\HttpFoundation\Request;

class InviteController extends Controller
{


    //会员推荐关系图
    public function childs()
    {
        return view("admin.invite.childs");
    }

    public function getTree(Request $request)
    {
        $limit = $request->get('limit', 20);
        $page = $request->get('page', 1);
        $id = $request->get('id', '');
        $parent_id = $request->get('parent_id', '');
        $model = new Users();
        if ($id) {
            $model = $model->where("id", $id);
        }
        if ($parent_id) {
            $model = $model->where("parent_id", $parent_id);
        }
        $feedbackList = $model->where("parent_id", ">", 0)->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
        return $this->layuiData($feedbackList);
    }

}

?>