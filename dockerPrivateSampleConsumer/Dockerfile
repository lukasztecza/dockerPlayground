FROM php:7.2.10-fpm-alpine3.8

ADD /consume.php /var/www/html/
ADD /composer.* /var/www/html/

RUN docker-php-ext-install bcmath sockets \
    && php composer.phar install --no-dev \
    && rm composer.phar

CMD ["php", "/var/www/html/consume.php"]
