#!/bin/bash

set -e

dirr=/usr/local/bin/apache2-foreground

for entry in "$dirr"/*
do
  echo "$entry"
done

export APACHE_RUN_DIR=/var/run/apache2$SUFFIX
export APACHE_RUN_USER=www-data
export APACHE_RUN_GROUP=www-data
export APACHE_PID_FILE=/var/run/apache2/apache2.pid
export APACHE_LOG_DIR=/var/log/apache2
export APACHE_SERVER_NAME=localhost

echo "ServerName localhost" >> /etc/apache2/apache2.conf

apache2 -D FOREGROUND

exec "$@"
