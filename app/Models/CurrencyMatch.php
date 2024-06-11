<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyMatch extends Model
{
    public $timestamps = false;

    protected $appends = [
        'legal_name',
        'currency_name',
        'price',
        'market_from_name',
        'change',
        'volume',
        'now_price',
        'rmb_relation',
        'logo'
    ];

    public function getRmbRelationAttribute()
    {
        return $this->currency()->value('rmb_relation');
    }

    protected static $marketFromNames = [
        0 => '无',
        1 => '交易所',
        2 => '火币接口',
        3 => '机器人',
        4 => '期货',
    ];

    protected function getLogoAttribute()
    {
        return $this->currency()->value('logo');
    }

    protected function getPriceAttribute()
    {
        return $this->currency()->value('price');
    }

    public function legal()
    {
        return $this->belongsTo(Currency::class, 'legal_id', 'id')->withDefault();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id')->withDefault();
    }

    public static function enumMarketFromNames()
    {
        return self::$marketFromNames;
    }

    public function getSymbolAttribute()
    {
        return $this->getCurrencyNameAttribute() . '/' . $this->getLegalNameAttribute();
    }

    public function getMatchNameAttribute()
    {
        return strtolower($this->getCurrencyNameAttribute() . $this->getLegalNameAttribute());
    }

    public function getLegalNameAttribute()
    {
        return $this->legal()->value('name');
    }

    public function getCurrencyNameAttribute()
    {
        return $this->currency()->value('name');
    }

    public function getMarketFromNameAttribute($value)
    {
        return self::$marketFromNames[$this->attributes['market_from']];
    }

    public function getCreateTimeAttribute($value)
    {
        return $value === null ? '' : date('Y-m-d H:i:s', $value);
    }

    public function getDaymarketAttribute()
    {
        $legal_id = $this->attributes['legal_id'];
        $currency_id = $this->attributes['currency_id'];
        CurrencyQuotation::unguard();
        $quotation = CurrencyQuotation::firstOrCreate([
            'legal_id' => $legal_id,
            'currency_id' => $currency_id,
        ], [
            'match_id' => $this->attributes['id'],
            'change' => '',
            'volume' => 0,
            'now_price' => 0,
            'add_time' => time(),
        ]);
        CurrencyQuotation::reguard();
        return $quotation;
    }

    public function getChangeAttribute()
    {
        return $this->getDaymarketAttribute()->change;
    }

    public function getVolumeAttribute()
    {
        return $this->getDaymarketAttribute()->volume;
    }

    public function getNowPriceAttribute()
    {
        return $this->getDaymarketAttribute()->now_price;
    }

    public function quotation()
    {
        return $this->hasOne(CurrencyQuotation::class, 'legal_id', 'legal_id');
    }

    public static function getHuobiMatchs()
    {
        $currency_match = self::with(['legal', 'currency'])
            ->where('market_from', 2)
            ->get();
        $huobi_symbols = HuobiSymbol::pluck('symbol')->all();
        $currency_match->transform(function ($item, $key) {
            $item->addHidden('currency');
            $item->addHidden('legal');
            $item->append('match_name');
            return $item;
        });
        //过滤掉不在火币中的交易对
        $currency_match = $currency_match->filter(function ($value, $key) use ($huobi_symbols) {
            return in_array($value->match_name, $huobi_symbols);
        });

        return $currency_match;
    }

    public static function getsystemMatchs()
    {
        $currency_match = self::with(['legal', 'currency'])
            ->where('market_from', 3)
            ->get();
        $currency_match->transform(function ($item, $key) {
            $item->addHidden('currency');
            $item->addHidden('legal');
            $item->append('match_name');
            return $item;
        });
        return $currency_match;
    }

    public function getRiskGroupResultNameAttribute()
    {

        $risk_list = [
            -1 => '亏损',
            0 => '无',
            1 => '盈利',
        ];
        $risk = $this->attributes['risk_group_result'] ?? 0;
        return $risk_list[$risk];
    }
}
