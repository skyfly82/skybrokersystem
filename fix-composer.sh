#!/bin/bash

echo "🔧 Naprawianie composer.json..."

# 1. Usuń problematyczne providery z config/app.php
if [ -f config/app.php ]; then
    echo "Usuwanie Laravel\Pail\PailServiceProvider z config/app.php..."
    sed -i '/Laravel\\Pail\\PailServiceProvider/d' config/app.php
fi

# 2. Napraw autoload w composer.json
if [ -f composer.json ]; then
    echo "Aktualizowanie composer.json..."
    
    # Utwórz kopię zapasową
    cp composer.json composer.json.backup
    
    # Utwórz poprawny composer.json
    cat > composer.json << 'EOF'
{
    "name": "skybrokersystem/skybrokersystem",
    "type": "project",
    "description": "SkyBrokerSystem - Advanced Courier Broker System",
    "keywords": ["laravel", "courier", "broker", "shipping"],
    "license": "MIT",
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "laravel/tinker": "^2.9"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/pint": "^1.13",
        "laravel/sail": "^1.26",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.0",
        "phpunit/phpunit": "^11.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
EOF

    echo "✅ composer.json został naprawiony"
fi

# 3. Usuń problematyczne pliki autoload jeśli istnieją
if [ -f app/Services/Payment/Notification/Notification.php ]; then
    echo "Usuwanie niepoprawnego pliku Notification.php..."
    rm app/Services/Payment/Notification/Notification.php
fi

if [ -f app/Services/Courier/Courier.php ]; then
    echo "Usuwanie niepoprawnego pliku Courier.php..."
    rm app/Services/Courier/Courier.php
fi

# 4. Utwórz podstawową strukturę katalogów
mkdir -p app/Services/{Notification,Courier,Payment}
mkdir -p config
mkdir -p bootstrap
mkdir -p database/{factories,seeders,migrations}
mkdir -p public
mkdir -p resources/{views,js,css}
mkdir -p routes
mkdir -p storage/{app,framework,logs}
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p tests

# 5. Utwórz podstawowy bootstrap/app.php jeśli nie istnieje
if [ ! -f bootstrap/app.php ]; then
    cat > bootstrap/app.php << 'EOF'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
EOF
fi

# 6. Usuń composer.lock żeby wymusić świeżą instalację
if [ -f composer.lock ]; then
    echo "Usuwanie composer.lock..."
    rm composer.lock
fi

echo "✅ Naprawiono problemy z composer"
echo "📁 Struktura katalogów utworzona"
echo "🚀 Możesz teraz uruchomić: make build"