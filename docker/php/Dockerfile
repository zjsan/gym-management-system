# 1. Base Image
FROM php:8.4-fpm-bullseye

# 2. Timezone configuration
ENV TZ=Asia/Manila
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 3. Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql bcmath gd zip

# 4. Copy Composer binary from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. User Management (WSL/Linux compatibility)
RUN groupadd -g 1000 www-data-group \
    && useradd -u 1000 -ms /bin/bash -g www-data-group www-user

# 6. Set Working Directory
WORKDIR /var/www/laravel

# 7. Copy Laravel subfolder contents into working directory
COPY ./laravel .

# 8. Set ownership before running composer
RUN chown -R www-user:www-data-group /var/www/laravel

# 9. Switch to non-root user and install dependencies
USER www-user
RUN composer install --no-interaction --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]