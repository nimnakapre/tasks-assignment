# Stage 1: Build Angular frontend
FROM node:20 as frontend-build
WORKDIR /app/frontend
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ .
RUN npm run build --configuration=production

# Stage 2: Backend with PHP & Apache
FROM php:8.2-apache
WORKDIR /var/www/html

# Install MariaDB (MySQL replacement) and other required packages
RUN apt-get update && apt-get install -y \
    mariadb-server \
    mariadb-client \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN mkdir -p /var/www/html/public

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy and install backend dependencies
COPY backend/composer.json backend/composer.lock /var/www/html/
WORKDIR /var/www/html
RUN composer install --no-scripts --no-autoloader
COPY backend/ /var/www/html/
RUN composer dump-autoload

# Copy frontend build output to public directory
COPY --from=frontend-build /app/frontend/dist/frontend/browser/index.html /var/www/html/public/
COPY --from=frontend-build /app/frontend/dist/frontend/browser/favicon.ico /var/www/html/public/
COPY --from=frontend-build /app/frontend/dist/frontend/browser/*.js /var/www/html/public/
COPY --from=frontend-build /app/frontend/dist/frontend/browser/*.css /var/www/html/public/
COPY --from=frontend-build /app/frontend/dist/frontend/browser/.htaccess /var/www/html/public/

# Configure Apache VirtualHost
RUN echo '\
<VirtualHost *:80>\n\
    ServerName localhost\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
\n\
    <Directory "/var/www/html/public">\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        DirectoryIndex index.html\n\
        FallbackResource /index.html\n\
    </Directory>\n\
\n\
    Alias /api /var/www/html/api\n\
    <Directory "/var/www/html/api">\n\
        Options -Indexes +FollowSymLinks +ExecCGI\n\
        AllowOverride All\n\
        Require all granted\n\
        SetHandler application/x-httpd-php\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    find /var/www/html/public -type f -exec chmod 644 {} \; && \
    find /var/www/html/public -type d -exec chmod 755 {} \;

# Expose port 80 for Apache
EXPOSE 80

# Start Apache when the container runs
CMD ["apache2-foreground"]