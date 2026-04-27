FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts

# Fix storage permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

<<<<<<< HEAD
<<<<<<< HEAD:dockerfile
CMD php artisan config:cache && php artisan serve --host=0.0.0.0 --port=8000
=======
CMD php artisan serve --host=0.0.0.0 --port=8000
>>>>>>> c57f7fba84bb4bfa138b09cdd9a0c198475c6ae2:Dockerfile
=======
CMD php artisan serve --host=0.0.0.0 --port=8000
>>>>>>> c57f7fba84bb4bfa138b09cdd9a0c198475c6ae2
