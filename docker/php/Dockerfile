FROM php:8.3-cli

# Встановлення системних залежностей
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libpq-dev libonig-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl mbstring opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Робоча директорія
WORKDIR /app
