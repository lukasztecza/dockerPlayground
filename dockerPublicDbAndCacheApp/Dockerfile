FROM php:7.2.10-fpm-alpine3.8

RUN docker-php-ext-install pdo pdo_mysql

RUN wget -O /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/5.1.1.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && rm -r /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv phpredis-* /usr/src/php/ext/redis
RUN docker-php-ext-install redis

ADD /public /var/www/html/public

EXPOSE 9000
