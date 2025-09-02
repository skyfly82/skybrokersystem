# SkyBrokerSystem 📦✈️

![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/skyfly82/skybrokersystem/laravel.yml?branch=main)
![Code Style](https://img.shields.io/badge/code%20style-pint-brightgreen)
![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue)
![Laravel Version](https://img.shields.io/badge/laravel-12.x-orange)
![License](https://img.shields.io/badge/license-MIT-lightgrey)

**SkyBrokerSystem** to nowoczesna platforma do zarządzania przesyłkami kurierskimi, zbudowana na frameworku **Laravel 12**. System implementuje architekturę **Modularnego Monolitu**, zapewniając wysoką skalowalność, bezpieczeństwo i łatwość w utrzymaniu.

## Główne Funkcjonalności

-   **Zarządzanie Przesyłkami**: Tworzenie, śledzenie i zarządzanie przesyłkami od wielu dostawców.
-   **Integracje z Kurierami**: Modułowa architektura pozwala na łatwe dodawanie nowych firm kurierskich (obecnie zintegrowany InPost).
-   **Przetwarzanie Płatności**: Wsparcie dla wielu bramek płatniczych (PayNow, Stripe) oraz trybu symulacyjnego.
-   **Panel Administracyjny**: Kompleksowy panel do zarządzania klientami, zamówieniami, płatnościami i ustawieniami systemu.
-   **Panel Klienta**: Intuicyjny interfejs dla klientów do zarządzania własnymi przesyłkami i płatnościami.
-   **RESTful API**: Bezpieczne API do integracji z zewnętrznymi systemami, chronione kluczami API oraz tokenami Sanctum.
-   **System Powiadomień**: Wielokanałowe powiadomienia (Email, SMS) dla kluczowych zdarzeń w systemie.

## Architektura i Stos Technologiczny

System został zaprojektowany z myślą o najlepszych praktykach i nowoczesnych wzorcach projektowych.

-   **Backend**: PHP 8.2+, Laravel 12
-   **Frontend**: Blade, Tailwind CSS, Alpine.js
-   **Baza Danych**: MySQL / PostgreSQL (z kluczami głównymi **UUID v7**)
-   **Cache/Kolejki**: Redis
-   **Architektura**: Modularny Monolit, Warstwa Usług (Service Layer), "Chude" Kontrolery.

## Wymagania

-   PHP >= 8.2
-   Composer 2.x
-   Node.js >= 16.x
-   Baza danych (MySQL >= 8.0 lub PostgreSQL >= 13)
-   Redis

## Instalacja i Uruchomienie

Poniższe kroki opisują proces instalacji w środowisku serwerowym (bez Dockera).

1.  **Klonowanie repozytorium**
    ```bash
    git clone [https://github.com/skyfly82/skybrokersystem.git](https://github.com/skyfly82/skybrokersystem.git)
    cd skybrokersystem
    ```

2.  **Instalacja zależności PHP**
    ```bash
    composer install --no-dev --optimize-autoloader
    ```

3.  **Konfiguracja środowiska**
    ```bash
    cp .env.example .env
    ```
    Następnie skonfiguruj plik `.env`, uzupełniając dane dostępowe do bazy danych, Redis oraz adres URL aplikacji (`APP_URL`).

4.  **Generowanie klucza aplikacji**
    ```bash
    php artisan key:generate
    ```

5.  **Migracje i dane startowe**
    ```bash
    php artisan migrate --seed
    ```

6.  **Dowiązanie katalogu storage**
    ```bash
    php artisan storage:link
    ```

7.  **Instalacja zależności i budowanie frontendu**
    ```bash
    npm install
    npm run build
    ```

8.  **Optymalizacja na produkcję**
    ```bash
    php artisan optimize
    ```

Po wykonaniu tych kroków aplikacja powinna być dostępna pod skonfigurowanym adresem URL.

## Testowanie

Do uruchomienia testów jednostkowych i funkcjonalnych służy następująca komenda:
```bash
php artisan test
npm run dev
