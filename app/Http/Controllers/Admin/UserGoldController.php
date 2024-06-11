<?php
/**
 * Created by PhpStorm.
 * User: 杨圣新
 * Date: 2018/10/26
 * Time: 16:39
 */

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\UserGold;

class UserGoldController extends Controller
{

    /**返回页面
     *
     */
    public function list()
    {
        return view('admin.user_gold.index');
    }

    /**返回列表数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function listData()
    {
        $limit = request()->input('limit', 10);
        $user_id = request()->input('user_id', 0);
        $result = new UserGold();
        if (!empty($user_id)) {
            $result = $result->where('user_id', $user_id);
        }
        $list = $result->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($list);
    }


}