FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    nodejs \
    npm \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
    pdo_mysql \
    mysqli \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy dependency files first (cache optimization)
COPY composer.json composer.lock ./
# COPY package.json package-lock.json ./
COPY package*.json ./

# Copy only files needed for Composer autoload/scripts
# COPY app ./app
# COPY bootstrap ./bootstrap
# COPY config ./config
# COPY artisan ./


# Copy full project
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# RUN npm install


# Set Laravel permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Switch user
USER www-data

EXPOSE 9000

CMD ["php-fpm"]