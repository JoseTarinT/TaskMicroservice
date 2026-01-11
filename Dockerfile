FROM php:8.2-cli

# Install PDO drivers
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libsqlite3-dev \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy source code
COPY composer.json ./

# Install dependencies
RUN composer install

# Copy the rest of the application code
COPY . .

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/router.php"]
