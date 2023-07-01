FROM node:16.20.1 AS node
FROM php:8.2.0-fpm

COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

RUN apt-get update && \
        apt-get install -y libpq-dev && \
        apt-get install -y libzip-dev zip &&\
        apt-get install -y python3 &&\
        apt-get install -y g++ build-essential &&\
        docker-php-ext-install pdo pdo_pgsql pgsql


RUN npm install -g yarn

#COPY --from=composer:2.4 /usr/bin/composer /usr/bin/composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY ./ /var/www

COPY .env.dist /var/www/.env

WORKDIR /var/www