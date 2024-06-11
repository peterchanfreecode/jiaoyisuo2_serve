<?php
/**
 * Created by PhpStorm.
 * User: 杨圣新
 * Date: 2018/10/26
 * Time: 16:39
 */

namespace App\Http\Controllers\Admin;
use App\Models\AccountLog;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\DepositOrder;
use App\Events\RebateEvent;
use App\Models\UsersWallet;
class DepositOrderController extends Controller
{

    /**返回页面
     *
     */
    public function list()
    {
        $currencies = Currency::where('is_display', 1)->orderBy('id', 'desc')->get();
        return view('admin.deposit_order.list')->with(['currencies' => $currencies]);
    }

    /**返回列表数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function listData()
    {
        $limit = request()->input('limit', 10);
        $account_number = request()->input('account_number', '');
        $currency = request()->input('currency', 0);
        $user_id = request()->input('user_id', 0);
        $status = request()->input('status', 0);
        $result = new DepositOrder();
        if (!empty($account_number)) {
            $result = $result->whereHas('user', function ($query) use ($account_number) {
                $query->where('account_number', 'like', '%' . $account_number . '%');
            });
        }
        if (!empty($currency)) {
            $result = $result->where('currency', $currency);
        }
        if (!empty($user_id)) {
            $result = $result->where('user_id', $user_id);
        }
        if ($status) {
            $result = $result->where('status', $status);
        }
        $list = $result->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($list);
    }

    public function edit(Request $request)
    {
        $id = $request->get('id', 0);
        $result = DepositOrder::find($id);
        return view('admin.deposit_order.edit')->with('result', $result);
    }

    public function postEdit(Request $request)
    {
        $desc = $request->get('desc', '');
        $id = $request->get('id', '');
        try {
            DepositOrder::where('id',$id)->update(['desc' => $desc]);
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }

    }
    public function end_order(Request $request)
    {
        $id = $request->get('id', '');
        $order =  DepositOrder::find($id);
        if(!$order){
            return $this->error("订单不存在");
        }
        if($order["status"] != 1){
            return $this->error("请勿重复结算");
        }
        try {
            DepositOrder::where('id', $order['id'])->update(['status' => 2]); // 订单已完成
            $user_wallet = UsersWallet::where('user_id', $order['user_id'])
                ->where('currency', $order['currency'])->lockForUpdate()->first();
            event(new RebateEvent($order['user_id'], $order['total_interest']));//质押利息返佣
            // 减冻结
            UsersWallet::decUserWallet($order['user_id'], $order['currency'], 'lock_change_balance', $order['amount']);
            change_wallet_balance($user_wallet, 2, $order['amount'], AccountLog::LH_LOAN, '质押到期,归还本金');
            change_wallet_balance($user_wallet, 2, $order['total_interest'], AccountLog::LH_LOAN, '质押到期,利息');
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }

    }


}