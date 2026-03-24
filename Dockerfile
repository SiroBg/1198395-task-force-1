FROM php:8.5.2-fpm-alpine3.22

RUN apk update && apk add --no-cache libzip-dev unzip && docker-php-ext-install zip

RUN apk add --no-cache --update \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    # Optional: Add libwebp-dev for WebP support
    libwebp-dev \
    # Clean up apk cache to keep the image size small
    && rm -rf /var/cache/apk/*

# Configure and install the GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install pdo pdo_mysql

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the application code
COPY ./composer.json ./composer.lock ./

# Run composer install to fetch dependencies

RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-dev
