FROM laravelsail/php84-composer:latest

RUN docker-php-ext-install pdo_mysql

# mysql client for setup/test DB creation
RUN apt-get update \
    && apt-get install -y --no-install-recommends default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

CMD ["/entrypoint.sh"]

