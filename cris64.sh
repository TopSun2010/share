#!/bin/bash

BUG=$(tail -n3 /var/run/miner.output | grep "sss")
if [ "$BUG" != "" ];then
sudo reboot
fi
