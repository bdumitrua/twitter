#!/bin/bash

for logfile in /var/log/kafka_*.log; do
    echo '' > $logfile
done
