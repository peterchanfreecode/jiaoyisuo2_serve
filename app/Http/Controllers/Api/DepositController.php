<?php

namespace App\Http\Controllers\Api;
use App\Events\CandyEvent;
use App\Models\AccountLog;
use App\Models\Deposit;
use App\Models\DepositOrder;
use App\Models\DepositOrderInterest;
use App\Service\DepositService;
use App\Models\Users;
use App\Models\UsersWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\RebateEvent;
class DepositController extends Controller
{

    // 配置
    public function config(Request $request)
    {
     /*   $language = session()->get('lang');*/
        $deposits = Deposit::orderBy('day', 'asc')->get()/*->map(function ($deposit) use ($language) {
            $deposit['level'] = $language != 'en' ? $deposit['level_zn'] : $deposit['level_en'];
            unset($deposit['level_zn'], $deposit['level_en']);
            return $deposit;
        })*/->toArray();
        return $this->success($deposits);
    }

    // 质押生息详情
    public function depositDetail(Request $request)
    {
        $id = intval($request->get('id', 0));
        $deposit = Deposit::leftJoin('currency', 'currency.id', '=', 'deposit.currency_id')
            ->where('deposit.id', $id)
            ->select('deposit.*', 'currency.name as currency_name')
            ->first();
        if (empty($deposit)) {
            return $this->error('网络错误');
        }
        $deposit = $deposit->toArray();
        $deposit['change_balance'] = UsersWallet::getBalance(Users::getUserId(), $deposit['currency_id']);
        $language = app('translator')->getLocale();
        switch ($language){
            case 'zh':
                $deposit['level'] = $deposit['level_zh'];
            case 'en':
                $deposit['level'] = $deposit['level_en'];
            case 'jp':
                $deposit['level'] = $deposit['level_jp'];
            case 'kr':
                $deposit['level'] = $deposit['level_kr'];
            case 'de':
                $deposit['level'] = $deposit['level_de'];
            case 'fra':
                $deposit['level'] = $deposit['level_fra'];
            default :
                $deposit['level'] = $deposit['level_en'];
        }
      /*  $deposit['level'] = $language == 'zh' ? $deposit['level_zh'] : $deposit['level_en'];*/
        unset($deposit['level_zh'], $deposit['level_en'], $deposit['level_jp'],
            $deposit['level_kr'], $deposit['level_de'],$deposit['level_fra'],$deposit['created_at'], $deposit['updated_at']);
        return $this->success($deposit);
    }

    // 详情
    public function detail(Request $request)
    {
        $id = intval($request->get('id', 0));
        $num = $request->get('num', 0);
        if ($num < 0) {
            return $this->error('请输入存币数量');
        }
        $deposit = Deposit::find($id);
        if (empty($deposit)) {
            return $this->error('网络错误');
        }
        if ($num < $deposit['save_min']) {
            return $this->error('不得低于最低存币数量');
        }
        $arr = [
            'id' => $id,
            'rate' => $deposit['rate'],
            'num' => $num,
            'income' => DepositService::getOutPut($num, $deposit['rate'], $deposit['day']),
            'change_balance' => UsersWallet::getBalance(Users::getUserId(), $deposit['currency_id'])
        ];
        return $this->success($arr);
    }

    // 订单
    public function order(Request $request)
    {
        $limit = $request->get('limit', 15);
        $page = $request->get('page', 1);
        $user_id = Users::getUserId();
        $order = DepositOrder::where('user_id', $user_id)
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
        if($order->items()){
            foreach($order->items() as &$v){
                $deposit = Deposit::where('id',  $v['deposit_id'])->first();
                $cancel_rate  = $deposit->cancel_rate??0;
                $cancel_rate =$cancel_rate/100;
                $today = Carbon::today()->startOfDay()->toDateString();
                $startDay = $today > $v->start_at ? $today : $v->start_at;
                $days = Carbon::parse($v->end_at)->diffInDays($startDay) + 1;
                $v['cancel_fee'] = $cancel_rate * $v['amount'] * $days;
                $v['cancel_fee'] = min($v['cancel_fee'], $v['amount']);
                $v['cancel_rate'] = $deposit['cancel_rate'];
            }
        }
        return $this->success([
                "list" => $order->items(),
                'count' => $order->total(),
                "page" => $page,
                "limit" => $limit
            ]
        );
    }
    //取消
    public function cancel(Request $request){
        $id =intval($request->get('id', 0));
        $deposit_order = DepositOrder::where('id', $id)->first();
        if (!$deposit_order) {
            return $this->error('订单不存在');
        }
        $userId = Users::getUserId();
        try {
            DB::beginTransaction();
            $deposit_order->status = -1;
            $user_wallet = UsersWallet::where('user_id', $userId)
                ->where('currency', $deposit_order['currency'])->lockForUpdate()->first();
            $deposit = Deposit::where('id',  $deposit_order->deposit_id)->first();
            $cancel_rate = $deposit->cancel_rate/100;
            $today = Carbon::today()->startOfDay()->toDateString();
            $startDay = $today > $deposit_order->start_at ? $today : $deposit_order->start_at;
            $days = Carbon::parse($deposit_order->end_at)->diffInDays($startDay) + 1;
            $cancel_fee = $cancel_rate * $deposit_order['amount'] * $days;
            $cancel_fee = min($cancel_fee, $deposit_order->amount);
            DepositOrder::where('id', $id)->update([
                'cancel_fee' => $cancel_fee,
                'status' => -1,
                'termination_at' => date("Y-m-d H:i:s",time()),

            ]);

            //计算利息
            $total = DepositOrderInterest::where("user_id",$userId)->where("order_id",$deposit_order->id)->sum("interest");
            // 增加冻结
            UsersWallet::decUserWallet($userId, $deposit['currency_id'], 'lock_change_balance', $deposit_order->amount);
            // 归还本金
            change_wallet_balance($user_wallet, 2, $deposit_order->amount, AccountLog::LH_LOAN, '取消质押,归还本金');
            // 给利息
            change_wallet_balance($user_wallet, 2, $total, AccountLog::LH_LOAN, '取消质押，给予利息');
            event(new RebateEvent($userId,$total));//质押利息返佣
            // 扣除违约金
            change_wallet_balance($user_wallet, 2, -$cancel_fee, AccountLog::LH_LOAN, '取消质押,扣除违约金');
            DB::commit();
            return $this->success('操作成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('网络错误');
        }

    }
    // 锁仓(购买)
    public function buy(Request $request)
    {
        $id = intval($request->get('id', 0));
        $num = $request->get('num', 0);
        if ($num < 0) {
            return $this->error('请输入存币数量');
        }
        $deposit = Deposit::where('id', $id)->first();
        if (empty($deposit)) {
            Log::info('非法参数:'.$id);
            return $this->error('参数错误');
        }
        if ($num < $deposit['save_min']) {
            return $this->error('不得低于最低存币数量');
        }
        $userId = Users::getUserId();
        try {
            DB::beginTransaction();
//            $balance = UsersWallet::getBalanceLock(Users::getUserId(), $deposit['currency_id']);
//            if ($balance < $num) {
//                return $this->error('余额不足');
//            }
            $user_wallet = UsersWallet::where('user_id', $userId)
                ->where('currency', $deposit['currency_id'])->lockForUpdate()->first();
            change_wallet_balance($user_wallet, 2, -$num, AccountLog::LH_LOAN, '质押生息,冻结资金');
            UsersWallet::incUserWallet($userId, $deposit['currency_id'], 'lock_change_balance', $num);
            //UsersWallet::decUserWallet($userId, $deposit['currency_id'], 'change_balance', $num);
            $depositOrder = [
                'user_id' => $userId,
                'deposit_id' => $deposit['id'],
                'currency' => $deposit['currency_id'],
                'amount' => $num,
                'total_interest' => DepositService::getOutPut($num, $deposit['rate'], $deposit['day']),
                'day_rate' => $deposit['rate'],
                'start_at' => Carbon::tomorrow()->format('Y-m-d'),
                'end_at' => Carbon::tomorrow()->addDays($deposit['day'] - 1)->format('Y-m-d'),
            ];
            DepositOrder::saveOrder($depositOrder);
            event(new CandyEvent($userId,$num));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('锁仓失败');
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return $this->error($e->getMessage());
        }
        return $this->success('操作成功');
    }

    // 统计
    public function census()
    {
        $userId = Users::getUserId();
        $arr['tgzj'] = DepositOrder::whereRaw('user_id = ? and status = 1', [$userId])->sum('amount'); // 正在托管的资金
        $arr['jrsy'] = DepositOrder::todayMay($userId); // 今日预估收益
        $arr['ljsy'] = DepositOrderInterest::where('user_id', $userId)->sum('interest'); // 累计收益
        $arr['tgdd'] = DepositOrder::whereRaw('user_id = ? and status = 1', [$userId])->count(); // 托管的订单
        return $this->success($arr);
    }


}

?>