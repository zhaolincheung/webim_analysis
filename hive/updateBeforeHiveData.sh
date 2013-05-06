#!/bin/sh

for((i=48;i<=116;i++));#115 
do 
    echo -e $i":";
    day=`date -d "-"$i" day" "+%Y-%m-%d"`;
    /usr/local/webserver/php/bin/php /data/webim/analys/hive/updateBeforeHiveData.php $day;
    echo "";
done
