#!/bin/bash
#while true
#do
PORT=$(grep "proxypool1" /home/ethos/local.conf |awk -F ':' '{print $2}')
echo $PORT
WORKER=$(hostname)
if [ "$PORT" = "8008" ];then
killall claymore
cd /opt/miners/claymore/ && ./claymore -epool eth.f2pool.com:8008 -ewal 0x37224647aa0b131b4ced61ce77dde7f278890a6e -eworker $WORKER -epsw x  -asm 2 >>/var/run/miner.output 2>&1 &
sleep 270
killall claymore
#sleep 430
#BUG=$(tail -n1 /var/run/miner.output | grep "sssssssssssssss")
#if [ "$BUG" != "" ];then
#sudo reboot
#fi
fi
if [ "$PORT" = "3333" ];then
killall claymore
cd /opt/miners/claymore/ && ./claymore -epool cn.sparkpool.com:3333 -ewal 0x37224647aa0b131b4ced61ce77dde7f278890a6e -eworker $WORKER -epsw x  -asm 2 >>/var/run/miner.output 2>&1 &
sleep 270
killall claymore
#sleep 430
#BUG=$(tail -n1 /var/run/miner.output  |grep "sssssssssssssss")
#if [ "$BUG" != "" ];then
#sudo reboot
#fi
fi
if [ "$PORT" = "9030" ];then
exit 1
fi
#done