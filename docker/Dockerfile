FROM php:8.0-apache

RUN apt-get update && apt-get install -y \
    libzip-dev unzip vim less libgd-dev \
    libpng-dev libwebp-dev libjpeg62-turbo-dev \
    libxpm-dev libfreetype6-dev && \
    docker-php-ext-configure gd \
    --enable-gd \
    --with-webp \
    --with-jpeg \
    --with-xpm \
    --with-freetype && \
    docker-php-ext-install mysqli zip gd && \
    apt-get remove -y libzip-dev libgd-dev \
    libpng-dev libwebp-dev libjpeg62-turbo-dev \
    libxpm-dev libfreetype6-dev && \
    rm -rf /var/lib/apt/lists/* && \
    /usr/sbin/a2enmod rewrite

COPY configs/business_encode.tar.gz /var/www/html/

RUN tar -xzf /var/www/html/business_encode.tar.gz -C /var/www/html && \
    rm /var/www/html/business_encode.tar.gz

ADD configs/php_bitrix.ini /usr/local/etc/php/conf.d/

RUN mkdir -p /var/www/html/bitrix/php_interface/include/sale_payment/ && \
    chown -R www-data.www-data /var/www/html

VOLUME ["/var/www/html"]

COPY docker-entrypoint.sh /usr/local/bin/

RUN ["chmod", "+x", "/usr/local/bin/docker-entrypoint.sh"]

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]