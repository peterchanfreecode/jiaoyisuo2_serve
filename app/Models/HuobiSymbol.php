<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HuobiSymbol extends Model
{
    public $timestamps = false;

    public static function getSymbolsData($symbols)
    {
        self::unguard();
        foreach ($symbols as $key => $value) {
            $arr = [
                'base-currency' => $value["base-currency"],
                'quote-currency' => $value["quote-currency"],
                'price-precision' => $value["price-precision"],
                'amount-precision' => $value["amount-precision"],
                'symbol-partition' => $value["symbol-partition"],
                'symbol' => $value["symbol"],
            ];
            $huobi_symbol = new self();
            $huobi_symbol->fill($arr)->save();
        }
        self::reguard();
        return true;
    }
}
