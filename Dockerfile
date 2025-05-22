FROM php:8.1-apache

# Extensions PHP
RUN apt-get update \
  && apt-get install -y libpng-dev libonig-dev libxml2-dev zip unzip git \
  && docker-php-ext-install pdo_mysql mbstring xml gd

# Composer & PHPMailer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev --optimize-autoloader

# Copier le projet
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
