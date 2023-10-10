FROM php:8.2-cli

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y libzip-dev zlib1g-dev openssl zip unzip git
RUN docker-php-ext-install zip

# Composer installing
# TODO better
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
# Composer installing end

# Redis
RUN pecl install redis && docker-php-ext-enable redis
#Redis end

WORKDIR /app/backend

COPY . .
RUN composer install

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0