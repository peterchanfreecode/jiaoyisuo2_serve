<?php
/**
 * Created by PhpStorm.
 * User: 杨圣新
 * Date: 2018/10/26
 * Time: 16:39
 */

namespace App\Http\Controllers\Admin;
use App\Models\ExceptionLog;
class ExceptionController extends Controller
{

    public function list()
    {
        return view('admin.exception.list');
    }
    /**返回列表数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function listData()
    {
        $limit = request()->input('limit', 10);
        $list = ExceptionLog::paginate($limit);
        return $this->layuiData($list);
    }

}