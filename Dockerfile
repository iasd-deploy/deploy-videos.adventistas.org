# STAGE1: Theme builder
FROM php:alpine AS theme-builder

RUN apk update 
RUN apk upgrade 
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.7/main/ nodejs=14.17.6-r0 
RUN apk add --no-cache bash lcms2-dev g++ make git pkgconfig autoconf automake libtool nasm build-base zlib-dev libpng libpng-dev jpeg-dev libc6-compat
RUN apk add --no-cache --update-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.12/community yarn=1.22.4-r0
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data app /var/www/build
# COPY --chown=www-data:www-data app /var/www/html

RUN composer clearcache

RUN cd /var/www/build/pt/wp-content/themes/pa-theme-sedes \
    && composer install --no-dev \
    && composer dump -o \
    && yarn \
    && yarn build:production \
    && rm -rf node_modules/

RUN cd /var/www/build/es/wp-content/themes/pa-theme-sedes \
    && composer install --no-dev \
    && composer dump -o \
    && yarn \
    && yarn build:production \
    && rm -rf node_modules/

RUN cd /var/www/build/pt/wp-content/themes/pa-theme-videos \
    && composer install --no-dev \
    && composer dump -o \
    && yarn \
    && yarn build:production \
    && rm -rf node_modules/

RUN cd /var/www/build/es/wp-content/themes/pa-theme-videos \
    && composer install --no-dev \
    && composer dump -o \
    && yarn \
    && yarn build:production \
    && rm -rf node_modules/



# STAGE2: Deploy wordpress
FROM wordpress AS site-build

COPY --from=wordpress:cli /usr/local/bin/wp /usr/local/bin/wp

COPY --chown=www-data:www-data --from=theme-builder /var/www/build /var/www/html

COPY extras/init /usr/local/bin/docker-entrypoint.sh

ARG WP_DB_HOST
ARG WP_DB_NAME
ARG WP_DB_PASSWORD
ARG WP_DB_USER
ARG WP_S3_ACCESS_KEY
ARG WP_S3_SECRET_KEY
ARG WP_S3_BUCKET
ARG NEWRELIC_KEY
ARG NEWRELIC_APP_NAME

ENV WP_DB_HOST=$WP_DB_HOST
ENV WP_DB_NAME=$WP_DB_NAME
ENV WP_DB_PASSWORD=$WP_DB_PASSWORD
ENV WP_DB_USER=$WP_DB_USER
ENV WP_S3_ACCESS_KEY=$WP_S3_ACCESS_KEY
ENV WP_S3_SECRET_KEY=$WP_S3_SECRET_KEY
ENV WP_S3_BUCKET=$WP_S3_BUCKET
ENV NEWRELIC_KEY=$NEWRELIC_KEY
ENV NEWRELIC_APP_NAME=$NEWRELIC_APP_NAME

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
