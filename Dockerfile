FROM php:8.1-apache

# 1) On change le DocumentRoot vers public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# 2) On met à jour les conf Apache : DocumentRoot, Directory, AllowOverride & activation de mod_rewrite
RUN sed -ri \
      -e 's!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g' \
      -e 's!<Directory /var/www/html>!<Directory ${APACHE_DOCUMENT_ROOT}>!g' \
      -e 's!AllowOverride None!AllowOverride All!g' \
      /etc/apache2/sites-available/000-default.conf \
      /etc/apache2/apache2.conf \
    && a2enmod rewrite

# 3) Extensions PHP nécessaires
RUN apt-get update \
  && apt-get install -y libpng-dev libonig-dev libxml2-dev zip unzip git \
  && docker-php-ext-install pdo_mysql mbstring xml gd

# 4) Composer & PHPMailer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- \
      --install-dir=/usr/local/bin --filename=composer \
  && composer install --no-dev --optimize-autoloader

# 5) Votre code source
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
