# syntax=docker/dockerfile:1.7-labs
FROM php:8.4-fpm

# set main params
ARG BUILD_ARGUMENT_ENV=dev
ENV ENV=$BUILD_ARGUMENT_ENV
ENV APP_HOME=/var/www/html
ARG HOST_UID=1000
ARG HOST_GID=1000
ENV USERNAME=www-data
ARG INSIDE_DOCKER_CONTAINER=1
ENV INSIDE_DOCKER_CONTAINER=$INSIDE_DOCKER_CONTAINER
ARG XDEBUG_CONFIG=main
ENV XDEBUG_CONFIG=$XDEBUG_CONFIG
ARG XDEBUG_VERSION=3.4.2
ENV XDEBUG_VERSION=$XDEBUG_VERSION
ENV PHP_CS_FIXER_IGNORE_ENV=1

# check environment
RUN if [ "$BUILD_ARGUMENT_ENV" = "default" ]; then echo "Set BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev" && exit 2; \
    elif [ "$BUILD_ARGUMENT_ENV" = "dev" ]; then echo "Building development environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "test" ]; then echo "Building test environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "staging" ]; then echo "Building staging environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then echo "Building production environment."; \
    else echo "Set correct BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev. Available choices are dev,test,staging,prod." && exit 2; \
    fi

# install system deps + PHP extensions (gd + exif inclus)
RUN set -eux; \
    apt-get update && apt-get upgrade -y && apt-get install -y \
      bash-completion \
      fish \
      procps \
      nano \
      git \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      libjpeg62-turbo-dev \
      libpng-dev \
      libfreetype6-dev \
      libwebp-dev \
      libxpm-dev \
      supervisor \
      cron \
      sudo \
      libzip-dev \
      wget \
      librabbitmq-dev \
      debsecan \
      xalan \
    ; \
    pecl install amqp; \
    docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd; \
    docker-php-ext-configure intl; \
    yes '' | pecl install -o -f redis; \
    docker-php-ext-enable redis; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm; \
    docker-php-ext-install -j"$(nproc)" \
      pdo_mysql \
      sockets \
      intl \
      opcache \
      zip \
      gd \
      exif \
    ; \
    docker-php-ext-enable amqp exif; \
    rm -rf /tmp/* /var/lib/apt/lists/*; \
    apt-get clean
