FROM php:8.1-fpm-alpine

# Cài đặt các gói phụ trợ hệ điều hành
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

# Cấu hình và cài đặt các PHP extensions cần thiết cho WordPress & WooCommerce
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

# Cài đặt extension Redis Object Cache qua PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

# Dọn dẹp các tệp build tạm thời để giảm dung lượng image
RUN apk del $PHPIZE_DEPS \
    && rm -rf /tmp/pear

# Thiết lập thư mục làm việc chuẩn cho WordPress
WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
