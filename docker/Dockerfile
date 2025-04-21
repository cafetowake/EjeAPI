FROM php:8.4.5-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

COPY ./backend/ /var/www/html/
