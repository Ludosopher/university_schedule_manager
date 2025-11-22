FROM php:8.0-fpm

# Create the APT partial directory to avoid permission issues
RUN mkdir -p /var/lib/apt/lists/partial

# Installing system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    gnupg \
    supervisor

# Installing Node.js and npm
RUN curl -fsSL https://nodejs.org/dist/v18.20.8/node-v18.20.8-linux-x64.tar.xz | tar -xJ -C /usr/local --strip-components=1 && \
    ln -s /usr/local/bin/node /usr/bin/node && \
    ln -s /usr/local/bin/npm /usr/bin/npm

# Clearing the cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installing PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installing Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copying the supervisor configuration file
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Creating a user for the application
RUN getent group www-data || groupadd -g 1000 www-data
RUN getent passwd www-data || useradd -u 1000 -ms /bin/bash -g www-data www-data

# Setting up caches for www-data
RUN mkdir -p /var/www/.npm /var/www/.composer && chown -R www-data:www-data /var/www/.npm /var/www/.composer

# Installing the working directory
WORKDIR /var/www/html

# Create public directory and set permissions
RUN mkdir -p public && chown -R www-data:www-data public

# Copying dependency files with proper ownership
COPY --chown=www-data:www-data composer.json composer.lock package.json package-lock.json ./
COPY --chown=www-data:www-data webpack.mix.js ./

# Copying the directories needed to generate autostart (seeds and factories)
COPY --chown=www-data:www-data database/seeds /var/www/html/database/seeds
COPY --chown=www-data:www-data database/factories /var/www/html/database/factories

# Copying webpack config and resources for asset compilation
COPY --chown=www-data:www-data resources/js ./resources/js
COPY --chown=www-data:www-data resources/sass ./resources/sass

# Configuring git
RUN git config --global --add safe.directory /var/www/html

# Switching to the www-data user
USER www-data

# Installing dependencies under www-data
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction \
    && npm ci --no-audit \
    && npm run dev

# Exposing the port
EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]