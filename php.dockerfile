FROM php:8.1.3-fpm-alpine

RUN mkdir -p /var/www/html

# ADD ./src/ /var/www/html

RUN apk --no-cache add shadow && usermod -u 1000 www-data

RUN docker-php-ext-install pdo pdo_mysql bcmath
