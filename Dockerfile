FROM php:7.3-apache

RUN apt-get update && apt-get install -y \
    curl \
    g++ \
    gettext \ 
    git \
    gnupg2 \
    libicu-dev \
    libpq-dev \
    libssl-dev \
    unzip \
    zlib1g-dev \
    mysql-client \
    wget

# Install nodejs
RUN curl -sL https://deb.nodesource.com/setup_11.x | bash -
RUN apt-get install -y nodejs 

# Install composer
RUN cd /tmp && \
    mkdir composer-setup && \
    cd composer-setup && \
    curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified' . PHP_EOL; } else { echo 'Installer corrupt' . PHP_EOL; unlink('composer-setup.php'); exit(1); } " && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Install wait-for-it
RUN \
    wget https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
        -O /usr/local/bin/wait-for-it && \
    chmod +x /usr/local/bin/wait-for-it

WORKDIR /opt/engelsystem
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install gettext 
RUN docker-php-ext-install pdo_mysql

ADD composer.json  /opt/engelsystem
RUN composer install

ADD package.json  /opt/engelsystem
RUN npm install

ADD . /opt/engelsystem
RUN npm run build

ADD config/config.docker.php config/config.php
ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

RUN a2enmod rewrite
CMD bin/docker-boot.sh
