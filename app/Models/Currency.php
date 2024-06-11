<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currency';
    public $timestamps = false;
    protected $appends = ['to_pb_price'];
    protected $hidden = ['key'];

    /**
     * 定义一对多关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quotation()
    {
        return $this->hasMany(CurrencyMatch::class, 'legal_id', 'id')->orderBy('sort', 'desc');
    }


    public function microNumbers()
    {
        return $this->hasMany(MicroNumber::class)->orderBy('number', 'asc');
    }

    public function getCreateTimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['create_time']);
    }
    public function getNowPriceAttribute()
    {
        $this->quotation()->value('now_price') ?? 1;

    }
    public static function getNameById($currency_id)
    {
        $currency = self::find($currency_id);
        return $currency->name;
    }

    //获取币种相对于人民币的价格
    public static function getCnyPrice($currency_id)
    {
        $rate = Setting::getValueByKey('USDTRate', 7.08);
        $usdt = Currency::where('name', 'USDT')->select(['id'])->first();
        $last = MarketHour::orderBy('id', 'desc')
            ->where("currency_id", $currency_id)
            ->where("legal_id", $usdt->id)->first();
        if (!empty($last)) {
            $cny_Price = $last->highest * $rate; //行情表里面最近的数据的最高值
        } else {
            $currency = CurrencyQuotation::where(["legal_id"=>3,'currency_id'=>$currency_id])->first();
            $cny_Price = $currency->price??0 * $rate;

        }
        if ($currency_id == $usdt->id) {
            $cny_Price = 1 * $rate;
        }

        return $cny_Price;
    }


    public function getRmbRelationAttribute()
    {
        $rate = Setting::getValueByKey('USDTRate', 7.08);
        return $rate;
    }

    public function getToPbPriceAttribute()
    {
        $currency_id = $this->id;
        $ptb = Currency::where('name', UsersWallet::CURRENCY_DEFAULT)->first();
        if($ptb){
            if (!empty($last)) {
                $Price = $last->highest; //行情表里面最近的数据的最高值
            } else {
                $Price = $ptb->price; //如果不存在交易对，默认为1
            }
            if ($currency_id == $ptb->id) {
                $Price = 1;
            }
            $to_pb_price = bcdiv($this->price, $Price, 8);
            return $to_pb_price;
        }
        return "";

    }
    //获取币种相对于平台币的价格
    public static function getPbPrice($currency_id)
    {
        $ptb = Currency::where('name', UsersWallet::CURRENCY_DEFAULT)->first();

        if($ptb){
            $last = MarketHour::orderBy('id', 'desc')
                ->where("currency_id", $currency_id)
                ->where("legal_id", $ptb->id)->first();
            if (!empty($last)) {
                $Price = $last->highest; //行情表里面最近的数据的最高值
            } else {
                $Price = $ptb->price; //如果不存在交易对，默认为1
            }
            if ($currency_id == $ptb->id) {
                $Price = 1;
            }
            return $Price;
        }
        return '';

    }

    public function getOriginKeyAttribute($value)
    {
        $private_key = $this->attributes['key'] ?? '';
        return $private_key != '' ? decrypt($private_key) : '';
    }

    public function getKeyAttribute($value)
    {
        return $value == '' ?: '********';
    }

    public function setKeyAttribute($value)
    {
        if ($value != '') {
            return $this->attributes['key'] = encrypt($value);
        }
    }
/*    public function getLogoAttribute()
    {
        $value = $this->attributes['logo'];
        if(strpos($value,'http')){
            return $value;
        }else{
            return  env("AWS_URL").$value;
        }
    }*/
}
