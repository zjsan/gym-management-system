# 1. Use PHP 8.4 for the latest features
FROM php:8.4-fpm-bullseye

# 2. Set working directory to match your project structure
WORKDIR /var/www/laravel

# 3. Timezone configuration
ENV TZ=Asia/Manila
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 4. Install system dependencies + PHP Extensions
# Added: libpng/libjpeg (for images), libzip (for backups/exports)
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_mysql bcmath gd zip

# 5. Modern way to get Composer (Cleaner than readfile)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. User Management (Crucial for WSL/Linux)
RUN groupadd -g 1000 www-data-group \
    && useradd -u 1000 -ms /bin/bash -g www-data-group www-user

# Set permissions for the working directory
RUN chown -R www-user:www-data-group /var/www/laravel

USER www-user

EXPOSE 9000
CMD ["php-fpm"]