<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserAccount extends Model
{
    protected $table = 'user_account';

    const ACCOUNT_DEPOSIT = 1; // 类型 。质押生息

    const ACCOUNT_DEPOSIT_CANCEL = 2; // 取消质押

    const ACCOUNT_DEPOSIT_OVER = 3; // 质押到期

    const SYMBOL_ADD = 0; // 增加

    const SYMBOL_SUB = 1; // 减少

    public static function addUserAccount($userId, $currency, $type, $symbol, $num, $balance)
    {
        $arr = [
            'user_id' => $userId,
            'currency' => $currency,
            'type' => $type,
            'symbol' => $symbol,
            'num' => $num,
            'balance' => $balance
        ];
        $model = new UserAccount();
        return $model->forceFill($arr)->save($arr);
    }
}
