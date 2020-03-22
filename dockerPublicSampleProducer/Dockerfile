FROM php:7.2.10-fpm-alpine3.8

ADD /public /var/www/html/public
ADD /stored.json /var/www/html/
ADD /composer.* /var/www/html/

RUN docker-php-ext-install bcmath sockets \
    && chown -R www-data:www-data /var/www/html/stored.json \
    && php composer.phar install --no-dev \
    && rm composer.phar

EXPOSE 9000
