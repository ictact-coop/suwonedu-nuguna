FROM php:5.6-apache

RUN apt-get -qq update && apt-get -qq -y --no-install-recommends install \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    mysql-client \
    && a2enmod rewrite

# install the PHP extensions we need
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli gd zip opcache

VOLUME /var/www/html

CMD ["apache2-foreground"]
