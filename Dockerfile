FROM php:7.3.24-apache
COPY ./config/php.ini /usr/local/etc/php/php.ini
RUN apt-get -y update \
&& apt-get upgrade -y \
  libicu-dev \
  git 
RUN set -eux; apt-get update; apt-get install -y libzip-dev zlib1g-dev mariadb-client; docker-php-ext-install zip
RUN a2enmod rewrite
RUN docker-php-ext-configure intl \
&& docker-php-ext-install intl
# mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql \
&& docker-php-ext-enable mysqli
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Downgrade to v.1
RUN composer self-update --1

WORKDIR /var/www/html
