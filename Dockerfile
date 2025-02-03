FROM php:8.2-fpm

# Installer les dépendances nécessaires pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Nettoyer le cache apt pour alléger l'image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
