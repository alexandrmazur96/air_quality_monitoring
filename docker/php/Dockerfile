FROM php:8.3.12-fpm

# Install Dependences
RUN apt-get update \
    && apt-get upgrade -yq \
    && apt-get install -yq apt-utils \
    libpq-dev  \
    libmcrypt-dev \
    libreadline-dev \
    libxslt-dev \
    libxml2-dev \
    libicu-dev \
    zlib1g-dev \
    libssl-dev \
    libzip-dev \
    unixodbc unixodbc-dev libonig-dev \
    wget \
    iputils-ping \
    telnet \
    mc \
    vim \
    htop \
    sudo

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

# Install PHP
RUN apt-get update \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install gettext \
    && docker-php-ext-install opcache \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install sockets \
    && docker-php-ext-install exif \
    && pecl install mcrypt && docker-php-ext-enable mcrypt

RUN pecl install redis-6.1.0 && docker-php-ext-enable redis

RUN apt-get update && apt-get install -y \
    && pecl install xdebug-3.3.2 \
    && docker-php-ext-enable xdebug

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

ARG USER_ID=1000
ARG GROUP_ID=1000
RUN userdel -f www-data && \
    if getent group www-data ; then groupdel www-data; fi && \
    groupadd -g ${GROUP_ID} www-data && \
    useradd -l -u ${USER_ID} -g www-data www-data && \
    install -d -m 0755 -o www-data -g www-data /home/www-data && \
    chown --changes --silent --no-dereference --recursive \
    --from=33:33 ${USER_ID}:${GROUP_ID} /var/www /home/www-data

USER www-data
