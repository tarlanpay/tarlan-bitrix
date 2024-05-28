#!/bin/bash

set -e

# if [ -d "/var/www/html/bitrix" ]; then
#   #ln -s /tarlanpayments /var/www/html/wa-plugins/payment/tarlanpayments
# fi

/usr/local/bin/apache2-foreground


exec "$@"
