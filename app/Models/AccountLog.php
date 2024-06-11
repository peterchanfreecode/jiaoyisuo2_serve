<?php

/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AccountLog extends Model
{
    protected $table = 'account_log';
    public $timestamps = false;
    const CREATED_AT = 'created_time';
    protected $appends = [
        'account_number',
        'account',
        'currency_name',//币种
        'before',//交易前
        'after',//交易后
        'transaction_info',//交易信息
        'lock_name',//交易信息
    ];
    const CHANGEREQ = 1;//充值
    const ADMIN_CHANGE_BALANCE = 2;//人工充值
    const WALLETOUT = 3;//提现
    const TRANSACTIONIN =4;//币币交易
    const TRANSACTION_FEE = 5;//币币交易手续费
    const  MICRO_TRADE = 6;//秒合约
    const  MICRO_TRADE_FREE = 7;//秒合约手续费
    const LH_LOAN = 8;//质押
    const IEO_BUY = 9; //IEO 申购
    const IEO_REFUSE = 10; // IEO拒绝
    const IEO_PASS = 11; // IEO通过
    const IEO_UN_THAW = 12; // 解冻
    const DEBIT_BALANCE_MINUS = 13; // 币币兑换
    const INVITE = 14;//邀请奖励
    const REBATE = 15;//返佣
    public function getAccountNumberAttribute()
    {
        return $this->hasOne(Users::class, 'id', 'user_id')->value('account_number');
    }

    public function getAccountAttribute()
    {
        $value = $this->hasOne(Users::class, 'id', 'user_id')->value('phone');
        if (empty($value)) {
            $value = $this->hasOne(Users::class, 'id', 'user_id')->value('email');
        }
        return $value;
    }

    public function getCreatedTimeAttribute()
    {
        $value = $this->attributes['created_time'];
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function getBeforeAttribute()
    {
        return $this->walletLog()->value('before');
    }

    public function getAfterAttribute()
    {
        return $this->walletLog()->value('after');
    }
    public function getLockNameAttribute()
    {
        $value =$this->walletLog()->value('lock_type');
        if($value == 1){
            return "<span style='color: red'>冻结账户";
        }else{
            return "<span style='color: green'>正常账户";
        }
    }
    public function getTransactionInfoAttribute()
    {
        $type1 = [
            '2' => '币币账户',
        ];
        $type2 = ['', '[锁定]'];
        $balance_type = $this->walletLog()->value('balance_type');
        $lock_tpye = $this->walletLog()->value('lock_type');
        array_key_exists($balance_type, $type1) ?: $balance_type = 0;
        array_key_exists($lock_tpye, $type2) ?: $lock_tpye = 0;
        return $type1[$balance_type] . $type2[$lock_tpye];

    }

    public function getCurrencyNameAttribute()
    {
        return $this->hasOne(Currency::class, 'id', 'currency')->value('name');
    }

    public static function insertLog($data = array(), $data2 = array())
    {
        $data = is_array($data) ? $data : func_get_args();
        $log = new self();
        $log->user_id = $data['user_id'] ?? false;;
        $log->value = $data['value'] ?? '';
        $log->created_time = $data['created_time'] ?? time();
        $log->info = $data['info'] ?? '';
        $log->type = $data['type'] ?? 0;
        $log->currency = $data['currency'] ?? 0;
        $data_wallet['balance_type'] = $data2['balance_type'] ?? 0;
        $data_wallet['wallet_id'] = $data2['wallet_id'] ?? 0;
        $data_wallet['lock_type'] = $data2['lock_type'] ?? 0;
        $data_wallet['before'] = $data2['before'] ?? 0;
        $data_wallet['change'] = $data2['change'] ?? 0;
        $data_wallet['after'] = $data2['after'] ?? 0;
        $data_wallet['memo'] = $data['info'] ?? 0;
        $data_wallet['create_time'] = $data2['create_time'] ?? time();
        $data_wallet['user_id'] = $data['user_id'] ?? 0;
        $data_wallet['from_user_id'] = $data['user_id'] ?? 0;
        try {
            DB::transaction(function () use ($log, $data_wallet) {
                $log->save();
                $log->walletLog()->create($data_wallet);
            });
            return true;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
            return false;
        }
    }

    public static function newinsertLog($data = array(), $data2 = array())
    {
        $data = is_array($data) ? $data : func_get_args();
        $log = new self();
        $log->user_id = $data['user_id'] ?? false;;
        $log->value = $data['value'] ?? '';
        $log->created_time = $data['created_time'] ?? time();
        $log->info = $data['info'] ?? '';
        $log->type = $data['type'] ?? 0;
        $log->currency = $data['currency'] ?? 0;
        try {
            DB::transaction(function () use ($log) {
                $log->save();
//                $log->walletLog()->create($data_wallet);
            });
            return true;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
            return false;
        }
    }


    public static function getTypeInfo($type)
    {
        switch ($type) {
            case self::ADMIN_CHANGE_BALANCE:
                return '后台调节币币账户余额';
                break;
            default:
                return '暂无此类型';
                break;
        }
    }
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

    //关联钱包记录模型
    public function walletLog()
    {
        return $this->hasOne(WalletLog::class, 'account_log_id', 'id')->withDefault();
    }
}
