FROM nginx:latest AS nginx_base

COPY ./ /var/www

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /etc/nginx/conf.d/
