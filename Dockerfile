FROM php:8.2-fpm

RUN apt update && apt upgrade -y

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer global require laravel/installer

RUN export PATH="$HOME/.composer/vendor/bin:$PATH"

WORKDIR /var/www/html/app

RUN composer update

RUN mv .env.prod .env

RUN php artisan migrate