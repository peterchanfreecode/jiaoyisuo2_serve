#!/bin/sh

dateCteate=`date "+%Y-%m-%d %H:%M:%S"`

ppid=$(ps -ef | grep python3 | grep main.py| awk '{ print $2 }')

if [[ $ppid -gt 1 ]];then
    echo $dateCteate "pid:" $ppid "huobi caiji runing..."
    echo $dateCteate "pid:" $ppid "huobi caiji runing..."  >>caiji.log
else
    echo $dateCteate "huobi caiji restart!!!"
    #重启程序
    nohup python3 /www/wwwroot/jiaoyisuo/jiaoyisuo2_serve/python/main.py >/dev/null 2>&1 &
    echo $dateCteate "huobi caiji restart!!!" >>caiji.log
fi
