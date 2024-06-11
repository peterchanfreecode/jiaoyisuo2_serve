<?php
namespace App\Service;


class DepositService
{

    // 计算产出
    public static function getOutPut($num, $rate, $day)
    {
        return bcmul(bcmul($num, $rate /100, 5), $day, 5);
    }

}