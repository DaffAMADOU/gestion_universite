FROM php:8.4-fpm-alpine 
 # 1. Installation des dépendances système critiques 
 RUN apk add --no-cache  git curl libpng-dev oniguruma-dev libxmldev 
 zip unzip mysql-client 
  # 2. Compilation et installation des extensions PHP requises par Laravel 
  RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath xml 
   # 3. Récupération propre du binaire Composer depuis son image officielle 
   COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer  
   # 4. Configuration du répertoire de travail
    WORKDIR /var/www  
    # 5. Copie du code applicatif existant 
    COPY . /var/www  
    # 6. Ajustement des permissions pour les répertoires de cache et de stockage Laravel 
    RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache  
EXPOSE 9000  
CMD ["php-fpm"] 