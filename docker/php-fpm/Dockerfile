FROM php:8-fpm-alpine

RUN apk --update --no-cache add bash \
   && apk add wget \
   && apk add git \
   && apk add php8 \
   && apk add php8-phar \
   && apk add php8-openssl \
   && apk add php8-xml \
   && apk add php8-pdo \
   && apk add php8-pdo_mysql \
   && apk add php8-mbstring \
   && apk add php8-iconv \
   && apk add php8-ctype \
   && apk add php8-posix \
   && apk add php8-intl \
   && apk add php8-opcache  \
   && apk add $PHPIZE_DEPS \
   && apk add icu-dev \
   && pecl install xdebug

ADD xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-enable intl

COPY php-overrides.ini /usr/local/etc/php/conf.d/

RUN mkdir -p /var/app
RUN adduser --disabled-password app && chown -R app:app /var/app

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Symfony installer
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN apk update && apk add alpine-conf
RUN setup-timezone -z Europe/Rome
COPY timezone.ini /usr/local/etc/php/conf.d/

RUN pecl install uopz
RUN docker-php-ext-enable uopz

USER app
WORKDIR /var/app

EXPOSE 9000

RUN echo "alias ll='ls -l'" >> ~/.bashrc
RUN echo "alias la='ls -la'" >> ~/.bashrc
