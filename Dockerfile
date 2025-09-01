# Utiliser une image PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les dépendances système nécessaires pour les extensions PHP
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libzip-dev \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP requises par l'application
RUN docker-php-ext-install pdo pdo_sqlite zip

# Activer le module de réécriture d'URL d'Apache (pour les .htaccess)
RUN a2enmod rewrite

# Définir le répertoire de travail
WORKDIR /var/www/html