#!/bin/bash

# Skrypt do utworzenia kompletnej struktury Docker dla SkyBrokerSystem v6

echo "🔧 Tworzenie struktury katalogów Docker..."

# Główne katalogi Docker
mkdir -p docker/{nginx,php,supervisor,mysql,redis}
mkdir -p docker/mysql/init

echo "📁 Struktura katalogów utworzona."

# 1. docker/nginx/nginx.conf
cat > docker/nginx/nginx.conf << 'EOF'
user nginx;
worker_processes auto;
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
    use epoll;
    multi_accept on;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';

    access_log /var/log/nginx/access.log main;

    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;

    gzip on;
    gzip_vary on;
    gzip_min_length 10240;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/x-javascript
        application/xml+rss
        application/javascript
        application/json
        image/svg+xml;

    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    limit_req_zone $binary_remote_addr zone=login:10m rate=1r/s;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    include /etc/nginx/conf.d/*.conf;
}
EOF

# 2. docker/nginx/default.conf
cat > docker/nginx/default.conf << 'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php;

    server_tokens off;
    
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        fastcgi_hide_header X-Powered-By;
        fastcgi_connect_timeout 60s;
        fastcgi_send_timeout 180s;
        fastcgi_read_timeout 180s;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ^~ /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ^~ /admin/login {
        limit_req zone=login burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ^~ /customer/login {
        limit_req zone=login burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location ~* \.(woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }

    location ~* \.(env|log|htaccess)$ {
        deny all;
    }

    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }

    location ^~ /webhooks/ {
        try_files $uri $uri/ /index.php?$query_string;
    }

    client_max_body_size 10M;
    
    error_page 404 /404.html;
    error_page 500 502 503 504 /50x.html;
}
EOF

# 3. docker/php/php.ini
cat > docker/php/php.ini << 'EOF'
[PHP]
engine = On
short_open_tag = Off
precision = 14
output_buffering = 4096
zlib.output_compression = Off
implicit_flush = Off
unserialize_callback_func =
serialize_precision = -1
disable_functions =
disable_classes =
zend.enable_gc = On

max_execution_time = 180
max_input_time = 60
memory_limit = 256M
post_max_size = 10M
upload_max_filesize = 10M
max_file_uploads = 20

log_errors = On
log_errors_max_len = 1024
ignore_repeated_errors = Off
ignore_repeated_source = Off
report_memleaks = On
track_errors = Off
html_errors = Off
error_log = /var/log/php_errors.log
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
display_errors = Off
display_startup_errors = Off

variables_order = "GPCS"
request_order = "GP"
register_argc_argv = Off
auto_globals_jit = On
auto_prepend_file =
auto_append_file =
default_mimetype = "text/html"
default_charset = "UTF-8"

file_uploads = On
upload_tmp_dir =
max_file_uploads = 20

allow_url_fopen = On
allow_url_include = Off
default_socket_timeout = 60

session.save_handler = files
session.use_strict_mode = 1
session.use_cookies = 1
session.use_only_cookies = 1
session.name = SKYBROKERSID
session.auto_start = 0
session.cookie_lifetime = 0
session.cookie_path = /
session.cookie_domain =
session.cookie_httponly = 1
session.cookie_secure = 0
session.cookie_samesite = "Lax"
session.serialize_handler = php
session.gc_probability = 1
session.gc_divisor = 1000
session.gc_maxlifetime = 1440

opcache.enable = 1
opcache.enable_cli = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
opcache.enable_file_override = 0
opcache.validate_timestamps = 0

realpath_cache_size = 4096K
realpath_cache_ttl = 600

date.timezone = Europe/Warsaw

mysqli.max_persistent = -1
mysqli.allow_persistent = On
mysqli.max_links = -1
mysqli.cache_size = 2000
mysqli.default_port = 3306
mysqli.reconnect = Off

pdo_mysql.cache_size = 2000

curl.cainfo = /etc/ssl/certs/ca-certificates.crt
openssl.cafile = /etc/ssl/certs/ca-certificates.crt
openssl.capath = /etc/ssl/certs
EOF

# 4. docker/php/php-fpm.conf
cat > docker/php/php-fpm.conf << 'EOF'
[global]
pid = /var/run/php-fpm.pid
error_log = /var/log/php-fpm.log
daemonize = no
emergency_restart_threshold = 10
emergency_restart_interval = 1m
process_control_timeout = 10s

[www]
user = www
group = www
listen = 127.0.0.1:9000
listen.owner = www
listen.group = www
listen.mode = 0660
listen.allowed_clients = 127.0.0.1

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
pm.process_idle_timeout = 10s

access.log = /var/log/php-fpm-access.log
access.format = "%R - %u %t \"%m %r%Q%q\" %s %f %{mili}d %{kilo}M %C%%"
slowlog = /var/log/php-fpm-slow.log
request_slowlog_timeout = 10s
request_terminate_timeout = 120s

clear_env = no
env[HOSTNAME] = $HOSTNAME
env[PATH] = /usr/local/bin:/usr/bin:/bin
env[TMP] = /tmp
env[TMPDIR] = /tmp
env[TEMP] = /tmp

php_admin_value[sendmail_path] = /usr/sbin/sendmail -t -i -f www@skybrokersystem.com
php_admin_flag[log_errors] = on
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 180
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M

php_admin_value[open_basedir] = /var/www/html:/tmp
EOF

# 5. docker/supervisor/supervisord.conf
cat > docker/supervisor/supervisord.conf << 'EOF'
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid
childlogdir=/var/log/supervisor
logfile_maxbytes=50MB
logfile_backups=10
loglevel=info

[unix_http_server]
file=/var/run/supervisor.sock
chmod=0700

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[supervisorctl]
serverurl=unix:///var/run/supervisor.sock

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
priority=10
stdout_logfile=/var/log/supervisor/nginx.log
stderr_logfile=/var/log/supervisor/nginx_error.log
user=root

[program:php-fpm]
command=php-fpm --nodaemonize
autostart=true
autorestart=true
priority=5
stdout_logfile=/var/log/supervisor/php-fpm.log
stderr_logfile=/var/log/supervisor/php-fpm_error.log
user=root
EOF

# 6. docker/mysql/my.cnf
cat > docker/mysql/my.cnf << 'EOF'
[client]
default-character-set = utf8mb4

[mysql]
default-character-set = utf8mb4

[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
init-connect = 'SET NAMES utf8mb4'

innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_type = 1
query_cache_size = 32M
tmp_table_size = 32M
max_heap_table_size = 32M
max_connections = 200
thread_cache_size = 16
table_open_cache = 2048

slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
log_queries_not_using_indexes = 1

binlog_format = ROW
expire_logs_days = 7
max_binlog_size = 100M

skip-name-resolve
bind-address = 0.0.0.0
sql_mode = STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO
EOF

# 7. docker/redis/redis.conf
cat > docker/redis/redis.conf << 'EOF'
bind 0.0.0.0
port 6379
timeout 300
tcp-keepalive 60

daemonize no
pidfile /var/run/redis_6379.pid
loglevel notice
logfile ""
databases 16

save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir /data

replica-serve-stale-data yes
replica-read-only yes

maxmemory 256mb
maxmemory-policy allkeys-lru

appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

slowlog-log-slower-than 10000
slowlog-max-len 128
EOF

# 8. Utworzenie podstawowych plików Laravel (jeśli nie istnieją)
if [ ! -f "package.json" ]; then
cat > package.json << 'EOF'
{
    "name": "skybrokersystem",
    "version": "6.0.0",
    "description": "SkyBrokerSystem - Advanced Courier Broker System",
    "private": true,
    "type": "module",
    "scripts": {
        "build": "vite build",
        "dev": "vite",
        "watch": "vite build --watch"
    },
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.7",
        "alpinejs": "^3.13.3",
        "autoprefixer": "^10.4.16",
        "axios": "^1.6.2",
        "laravel-vite-plugin": "^1.0.0",
        "postcss": "^8.4.32",
        "tailwindcss": "^3.3.6",
        "vite": "^5.0.10"
    },
    "dependencies": {
        "chart.js": "^4.4.0"
    }
}
EOF
fi

if [ ! -f "package-lock.json" ]; then
    echo "{}" > package-lock.json
fi

# 9. Tworzenie .env.docker (jeśli nie istnieje)
if [ ! -f ".env.docker" ]; then
cat > .env.docker << 'EOF'
APP_NAME="SkyBrokerSystem"
APP_ENV=production
APP_KEY=base64:your-generated-app-key-here
APP_DEBUG=false
APP_URL=http://localhost
APP_DOMAIN=localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=skybrokersystem
DB_USERNAME=skybroker
DB_PASSWORD=secure_password_here
DB_ROOT_PASSWORD=root_secure_password_here

REDIS_HOST=redis
REDIS_PASSWORD=redis_secure_password_here
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@skybrokersystem.com
MAIL_FROM_NAME="SkyBrokerSystem"

SMS_DRIVER=log
SMS_API_URL=
SMS_API_KEY=
SMS_SENDER_NAME=SkyBroker

PAYMENT_PAYNOW_API_KEY=
PAYMENT_PAYNOW_SIGNATURE_KEY=
PAYMENT_STRIPE_PUBLISHABLE_KEY=
PAYMENT_STRIPE_SECRET_KEY=

COURIER_INPOST_TOKEN=
COURIER_INPOST_ORGANIZATION_ID=
COURIER_DHL_USERNAME=
COURIER_DHL_PASSWORD=
EOF
fi

echo "✅ Wszystkie pliki konfiguracyjne Docker zostały utworzone!"
echo ""
echo "📁 Struktura katalogów:"
echo "docker/"
echo "├── nginx/"
echo "│   ├── nginx.conf"
echo "│   └── default.conf"
echo "├── php/"
echo "│   ├── php.ini"
echo "│   └── php-fpm.conf"
echo "├── supervisor/"
echo "│   └── supervisord.conf"
echo "├── mysql/"
echo "│   └── my.cnf"
echo "└── redis/"
echo "    └── redis.conf"
echo ""
echo "📄 Dodatkowe pliki:"
echo "├── package.json (utworzony)"
echo "├── package-lock.json (utworzony)"
echo "└── .env.docker (utworzony)"
echo ""
echo "🚀 Teraz możesz uruchomić: make setup"