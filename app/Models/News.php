<?php
/**
 * create by vscode
 * @author lion
 */
namespace App\Models;

class News extends ShopModel
{
    protected $table = 'news';
    //自动时间戳
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    protected static $langList = [
        'zh' => '中文简体',
        'en' => '英文',
        'jp' => '日语',
        'kr' => '韩国',
        'de' => '德国',
        'fra' => '法国',
        'th' => '泰语',
        'vi' => '越南语',
        'hk' => '中文繁体',
        'es' => '西班牙语',
        'pt' => '葡萄牙语',
    ];

    public static function getLangeList()
    {
        return self::$langList;
    }
    /**
     * 定义新闻和分类的一对多相对关联
     */

    public function cate()
    {
        return $this->belongsTo(NewsCategory::class, 'c_id');
    }

    /**
     * 定义新闻和评论的一对多关联
     */

    public function discuss()
    {
        return $this->hasMany('App\NewsDiscuss', 'n_id');
    }
    public function getCreateTimeAttribute()
    {
        $value = $this->attributes['create_time'];
        return $value ? date('Y-m-d H:i:s', $value ) : '';
    }
    public function getThumbnailAttribute()
    {
        $thumbnail = $this->attributes['thumbnail'];
        if($thumbnail){

            if (stristr($thumbnail, 'http')) {
                return $thumbnail;
            } else {
                return config('app.aws_url'). $thumbnail;
            }
        }else{
            return  URL("images/zwtp.png");
        }

    }

    public function getUpdateTimeAttribute()
    {
        $value = $this->attributes['update_time'];
        return $value ? date('Y-m-d H:i:s', $value ) : '';
    }
    protected static function boot(){
        

    }
    public function getCoverAttribute()
    {
        $value = $this->attributes['cover'];
        if (stristr($value, 'http')) {
            return $value;
        } else {
            return config('app.aws_url'). $value;
        }
    }
}
