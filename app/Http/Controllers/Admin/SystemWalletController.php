<?php
/**
 * Created by PhpStorm.
 * User: 杨圣新
 * Date: 2018/10/26
 * Time: 16:39
 */

namespace App\Http\Controllers\Admin;

use App\Models\AdminLog;
use App\Models\SystemWallet;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;
use App\Traits\CheckGoogleSecurityCodeTraint;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Redis;
use App\Models\ExceptionLog;
class SystemWalletController extends Controller
{
    use CheckGoogleSecurityCodeTraint;

    public function add()
    {
        if (request()->isMethod('GET')) {
            $currencies = Currency::orderBy('id', 'desc')->get();
            return view('admin.system_wallet.add')->with(['currencies' => $currencies]);
        }
        if (request()->isMethod('POST')) {
            $data['address'] = request()->input('address', '');
            $data['type'] = request()->input('type', '');
            $data['currency_id'] = request()->input('currency_id', 0);
            $data['stepcode'] = request()->input('stepcode', 0);
            foreach ($data as $v) if (!$v) return $this->error('请填写完整表单');
            $key = Redis::get("google_key");
            if (!$key) {
                return $this->error('安全码未设置');
            }
            if ($this->checkSecurityCode($data['stepcode'], $key) == false) {
                return $this->error('验证安全码失败');
            }
            DB::beginTransaction();
            $arr = explode(",", $data['address']);
            try {
                foreach ($arr as $v) {
                    if(!$v){
                        continue;
                    }
                    $model = new SystemWallet();
                    $model->create_at = date("Y-m-d H:i:s", time());
                    $model->currency_id = $data['currency_id'];
                    $model->address = trim($v);
                    $model->type = $data['type'];
                    $model->operator_time = date("Y-m-d H:i:s", time());
                    $model->operator_id = session()->get('admin_id');
                    $model->operator_name = session()->get('admin_username');
                    $model->save();
                    ExceptionLog::set_log( $data['type'],trim($v));
                }
                DB::commit();
                return $this->success('保存成功');
            } catch (\Exception $e) {
                DB::rollback();
                return $this->error($e->getMessage());
            }
        }
    }

    public function edit()
    {
        if (request()->isMethod('GET')) {

            $id = request()->input('id', 0);
            $result = SystemWallet::find($id);
            $currencies = Currency::orderBy('id', 'desc')->get();
            return view('admin.system_wallet.edit')->with(['currencies' => $currencies, 'result' => $result]);
        }

        if (request()->isMethod('POST')) {
            $data['address'] = request()->input('address', '');
            $data['type'] = request()->input('type', '');
            $data['currency_id'] = request()->input('currency_id', 0);
            $data['stepcode'] = request()->input('stepcode', 0);
            foreach ($data as $v) if (!$v) return $this->error('请填写完整表单');
            $id = request()->input('id', 0);
            $key = Redis::get("google_key");
            if (!$key) {
                return $this->error('安全码未设置');
            }
            if ($this->checkSecurityCode($data['stepcode'], $key) == false) {
                return $this->error('验证安全码失败');
            }
            $model = SystemWallet::find($id);
            if ($model->currency_id != $data['currency_id']) {
                return $this->error('编辑不能更改币种');
            }
            DB::beginTransaction();
            try {
                $model->currency_id = $data['currency_id'];
                $model->address = $data['address'];
                $model->type = $data['type'];
                $model->operator_time = date("Y-m-d H:i:s", time());
                $model->operator_id = session()->get('admin_id');
                $model->operator_name = session()->get('admin_username');
                $model->save();
                ExceptionLog::set_log( $data['type'],trim($data['address']));
                DB::commit();
                return $this->success('保存成功');
            } catch (\Exception $e) {
                DB::rollback();
                return $this->error($e->getMessage());
            }
        }
    }

    /**返回页面
     *
     */
    public function list()
    {
        return view('admin.system_wallet.list');
    }

    /**返回列表数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function listData()
    {
        $limit = request()->input('limit', 10);
        $list = SystemWallet::paginate($limit);
        return $this->layuiData($list);
    }


    public function delete()
    {
        $id = request()->input('id', 0);
        $model = SystemWallet::find($id);
        $stepcode = request()->input('stepcode', 0);
        if (!$model) return $this->error('地址不存在');
        /*  $step_code_info = AppSetting::where("key", "step_code")->first();*/
        $key = Redis::get("google_key");
        if (!$key) {
            return $this->error('安全码未设置');
        }
        if ($this->checkSecurityCode($stepcode, $key) == false) {
            return $this->error('验证安全码失败');
        }
        DB::beginTransaction();
        try {
            $info = $model->delete();
            if (!$info) throw new \Exception('删除失败');
            DB::commit();
            return $this->success('删除成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }
}