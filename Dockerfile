FROM php:8.1-apache


ENV APACHE_DOCUMENT_ROOT /var/www/html/public


RUN sed -ri \
    -e 's!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g' \
    -e 's!<Directory /var/www/html>!<Directory ${APACHE_DOCUMENT_ROOT}>!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
  && a2enmod rewrite

RUN apt-get update \
  && apt-get install -y libpng-dev libonig-dev libxml2-dev zip unzip git \
  && docker-php-ext-install pdo_mysql mbstring xml gd

WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev --optimize-autoloader


COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
