FROM php:8.2-apache

# Habilitar módulos de Apache necesarios para el ruteo MVC y optimización (.htaccess)
RUN a2enmod rewrite headers expires deflate

# Instalar extensiones de PHP requeridas para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Configurar Apache para permitir .htaccess (AllowOverride All)
RUN echo '<Directory /var/www/html>\n\
    Options FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf && a2enconf override

# Directorio de trabajo principal
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . /var/www/html/

# Crear directorios de logs y uploads con permisos adecuados para Apache (www-data)
RUN mkdir -p /var/www/html/logs /var/www/html/assets/uploads && \
    chown -R www-data:www-data /var/www/html

EXPOSE 80
