#!/bin/sh

dateCteate=`date "+%Y-%m-%d %H:%M:%S"`

ppid=$(ps -ef | grep -w  WorkerMan| grep -w artisan | head -n 1| awk '{ print $2 }')

if [[ $ppid -gt 1 ]];then
    echo $dateCteate "pid:" $ppid "websocket client runing..."
    echo $dateCteate "pid:" $ppid "websocket client runing..." >>wsclient.log
else
    # 重启服务
        pids=$(ps -ef|grep -w Huobi | grep -v grep | awk '{ print $2 }')
    for id in $pids
    do  
        kill -9 $id
        sleep 1;
    done
    echo $dateCteate "websocket client restart!!!" >>wsclient.log
    #重启程序
    nohup php /www/wwwroot/jiaoyisuo/jiaoyisuo2_serve/artisan websocket:client start >/dev/null 2>&1 &
    echo $dateCteate "websocket client restart!!!" >>wsclient.log
fi
