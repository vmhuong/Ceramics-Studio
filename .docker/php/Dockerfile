FROM php:8.1-fpm-alpine

RUN apk add --no-cache \
    bash \
    curl \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        mysqli \
        opcache \
        pdo_mysql \
        zip

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN apk del $PHPIZE_DEPS \
    && rm -rf /tmp/pear

# Tao thu muc log cho PHP-FPM slow log
RUN mkdir -p /var/log/php-fpm

# COPY config truc tiep vao image - khong can mount file nua
COPY .docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY .docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
