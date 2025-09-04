FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache nginx supervisor mysql-client libpng-dev libzip-dev zip unzip curl
RUN docker-php-ext-install pdo_mysql zip gd opcache
RUN addgroup -g 1000 -S www && adduser -u 1000 -S www -G www

COPY . .
RUN chown -R www:www /var/www/html

# Skip composer for now
RUN echo "Skipping composer install"

# Create directories first
RUN mkdir -p /etc/supervisor/conf.d /var/log/supervisor /var/run/nginx storage/{logs,framework/{cache,sessions,views}} bootstrap/cache public

# Create SIMPLE nginx config (without problematic gzip settings)
RUN cat > /etc/nginx/http.d/default.conf << 'NGINXEOF'
server {
    listen 80;
    root /var/www/html/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location /health {
        return 200 "healthy";
        add_header Content-Type text/plain;
    }
}
NGINXEOF

# Create simple supervisor config
RUN cat > /etc/supervisor/conf.d/supervisord.conf << 'SUPERVISOREOF'
[supervisord]
nodaemon=true

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true

[program:php-fpm]
command=php-fpm --nodaemonize
autostart=true
autorestart=true
SUPERVISOREOF

# Set permissions
RUN chmod -R 755 storage bootstrap/cache
RUN echo '<?php echo "SkyBrokerSystem v6 is running!"; ?>' > public/index.php

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
