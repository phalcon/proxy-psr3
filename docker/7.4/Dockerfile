FROM composer:latest as composer
FROM php:7.4-fpm

# Environment
ENV PHALCON_VERSION="5.0.2" \
    PHP_VERSION="7.4" \
    MY_USER="phalcon" \
	MY_GROUP="phalcon" \
	MY_UID="1000" \
	MY_GID="1000"

ADD ./extra.ini /usr/local/etc/php/conf.d/

# User and Group
RUN set -eux && \
	groupadd -g ${MY_GID} -r ${MY_GROUP} && \
	useradd -u ${MY_UID} -m -s /bin/bash -g ${MY_GROUP} ${MY_USER}

# Update
RUN apt update -y && \
    apt install -y \
        apt-utils \
        git \
        libzip-dev \
        locales \
        nano \
        sudo \
        wget \
        zip

# PECL Packages
RUN pecl install phalcon-${PHALCON_VERSION} xdebug

RUN docker-php-ext-install \
        zip

# Install PHP extensions
RUN docker-php-ext-enable \
        phalcon \
        xdebug

# Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
# Bash script with helper aliases
COPY ./.bashrc /root/.bashrc

CMD ["php-fpm"]
