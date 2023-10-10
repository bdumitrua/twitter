FROM php:8.2-cli

# Extensions instlling
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get install -y libzip-dev zlib1g-dev openssl zip unzip git
RUN docker-php-ext-install zip
# Extensions installing end

# Composer installing
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN docker-php-ext-install sockets
# Composer installing end

# Redis installing 
RUN pecl install redis && docker-php-ext-enable redis
#Redis installing end

WORKDIR /app/backend

COPY . .
RUN composer install

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0