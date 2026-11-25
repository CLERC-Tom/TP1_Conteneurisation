FROM php:8.3-fpm-alpine

# Extension PostgreSQL pour PDO ; les dépendances de compilation sont supprimées ensuite
RUN apk add --no-cache libpq \
 && apk add --no-cache --virtual .build-deps postgresql-dev \
 && docker-php-ext-install pdo_pgsql \
 && apk del .build-deps

COPY app/ /var/www/html/
