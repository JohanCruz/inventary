# Usa la imagen base de PHP FPM
FROM php:8.2.1-fpm

# Establece el directorio de trabajo en /app
WORKDIR /app

# Copia el contenido del directorio actual a /app en el contenedor
COPY . /app

# Instala Composer y las dependencias de Laravel
RUN apt-get update && \
    apt-get install -y \
    zip unzip && \
    docker-php-ext-install pdo pdo_mysql && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install

RUN composer install --no-dev --optimize-autoloader  
  
# Configura caché de Laravel y optimiza la aplicación
RUN php artisan config:cache
RUN php artisan route:cache

# Expone el puerto 8000
EXPOSE 8000

# Comando para iniciar el servidor
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
