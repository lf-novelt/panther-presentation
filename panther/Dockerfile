FROM php:fpm-alpine

RUN apk update && apk add bash

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Chromium and ChromeDriver
ENV PANTHER_NO_SANDBOX 1
# Not mandatory, but recommended
ENV PANTHER_CHROME_ARGUMENTS='--disable-dev-shm-usage'
RUN apk update && apk upgrade
RUN apk add libzip-dev zip chromium chromium-chromedriver
RUN docker-php-ext-install zip

EXPOSE 9000

#CMD ["php-fpm", "-F"]

WORKDIR /var/www/html/public
COPY src .
RUN composer install

CMD ["php", "./vendor/bin/phpunit"]