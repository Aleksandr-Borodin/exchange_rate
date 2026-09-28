FROM php:8.3.35-cli

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        curl \
        libicu-dev \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
        bcmath \
        sockets \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
RUN git config --global --add safe.directory /app