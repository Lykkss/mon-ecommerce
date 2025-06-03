FROM php:8.1-apache

# 1) On définit la racine web souhaitée
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# 2) On force la mise à jour de DocumentRoot & Directory,
#    et on active mod_rewrite pour .htaccess
RUN a2enmod rewrite \
 && sed -ri \
      -e "s!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g" \
      -e "s!<Directory /var/www/html>!<Directory ${APACHE_DOCUMENT_ROOT}>!g" \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf

# 3) On installe les extensions PHP dont vous avez besoin
RUN apt-get update \
 && apt-get install -y libpng-dev libonig-dev libxml2-dev zip unzip git \
 && docker-php-ext-install pdo_mysql mbstring xml gd

# 4) Installation de Composer & dépendances
WORKDIR /var/www/html
COPY composer.json ./ 
RUN curl -sS https://getcomposer.org/installer | php -- \
      --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev

# 5) Copie de votre code source
COPY . /var/www/html

# 6) Permissions justes
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
