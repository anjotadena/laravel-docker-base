FROM php:8.1.3-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql bcmath
