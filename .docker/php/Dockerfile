FROM php:8.1-fpm
WORKDIR "/app"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --no-install-recommends apt-utils

# Update packages & Install Git and Supervisor
RUN apt-get update -y && \
    apt-get install -yq git supervisor libpq-dev

# Install extensions
RUN docker-php-ext-configure pcntl
RUN docker-php-ext-install -j "$(nproc)" pcntl

# Install Swoole extension
RUN pecl install -f -D 'enable-openssl="no" enable-sockets="no" enable-http2="no" enable-mysqlnd="no" enable-hook-curl="no" with-postgres="no"' openswoole
RUN docker-php-ext-enable openswoole

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install -j "$(nproc)" pdo \
  pdo_pgsql

# Added supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisor.conf
CMD ["/usr/bin/supervisord", "-n"]
