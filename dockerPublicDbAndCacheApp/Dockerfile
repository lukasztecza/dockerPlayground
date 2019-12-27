FROM php:7.2.10-fpm-alpine3.8

RUN docker-php-ext-install pdo pdo_mysql

ADD /public /var/www/html/public

EXPOSE 9000
