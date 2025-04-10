FROM php:8.2-cli-alpine

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www
COPY . /var/www

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
