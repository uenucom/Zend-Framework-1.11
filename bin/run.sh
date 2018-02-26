#!/bin/sh
#10.11.8.98 
#/home/hadoop/scripts/wzj/hello.sh
line=$*
echo $line
password=tswcbyyslavehadoop
expect <<EOF

#spawn su - user
#spawn ssh -o StrictHostKeyChecking=no hadoop@10.11.8.98
spawn ssh -o StrictHostKeyChecking=no hadoop@10.11.8.78
expect "*password:" 
send "$password\r"
set timeout 1000000
#expect "*hadoop*"
#send "ll\r"
expect "*hadoop*"
send "cd /home/hadoop/ \r"
expect "*hadoop*"
send " \r"
expect "*hadoop*"
send "./hetong.sh $line \r"
expect "*hadoop*"
send "\r"
expect "*hadoop*"
send "exit\r"
interact

EOF
