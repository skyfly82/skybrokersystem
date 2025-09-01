# SkyBrokerSystem 📦✈️

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)

Zaawansowana platforma brokerska do zarządzania przesyłkami kurierskimi, zbudowana w oparciu o framework Laravel 11. System umożliwia kompleksową obsługę klientów, zarządzanie przesyłkami oraz integrację z API firm kurierskich.

## Spis Treści

1.  [Opis Projektu](#opis-projektu)
2.  [Główne Funkcjonalności](#główne-funkcjonalności)
3.  [Stos Technologiczny](#stos-technologiczny)
4.  [Instalacja i Uruchomienie](#instalacja-i-uruchomienie)
5.  [Konfiguracja](#konfiguracja)
6.  [Testowanie](#testowanie)
7.  [Licencja](#licencja)

---

### Opis Projektu

**SkyBrokerSystem** to nowoczesne rozwiązanie dla firm pośredniczących w usługach kurierskich. Aplikacja została zaprojektowana z myślą o skalowalności i wydajności, oferując dwa główne panele:
* **Panel Administracyjny:** do zarządzania całym systemem, klientami, usługami kurierskimi i finansami.
* **Panel Klienta:** intuicyjny interfejs dla klientów do nadawania i śledzenia przesyłek, zarządzania użytkownikami oraz generowania raportów.

---

### Główne Funkcjonalności

✅ **Panel Administracyjny:**
* Dashboard ze statystykami systemu.
* Zarządzanie klientami (firmami) i ich limitami kredytowymi.
* Przegląd wszystkich przesyłek w systemie.
* Zarządzanie usługami kurierskimi i ich konfiguracją.
* System ról i uprawnień dla administratorów.

✅ **Panel Klienta:**
* Dashboard ze statystykami przesyłek danego klienta.
* Proces tworzenia nowej przesyłki z wyceną i wyborem kuriera.
* Śledzenie statusu przesyłek w czasie rzeczywistym.
* Możliwość drukowania etykiet i anulowania przesyłek.
* Zarządzanie użytkownikami w ramach konta firmowego.
* Podstawowe raportowanie.

✅ **API:**
* RESTful API dla klientów do integracji z ich własnymi systemami (np. e-commerce).
* Bezpieczny dostęp oparty o klucze API.

---

### Stos Technologiczny

* **Backend:** Laravel 11, PHP 8.x
* **Frontend:** Blade, Tailwind CSS, JavaScript (Alpine.js)
* **Baza Danych:** MySQL 8
* **Serwer:** Nginx (zalecany)
* **Narzędzia:** Composer, Vite, NPM

### Moduł Map (OSM)

Nowy mikroserwis Mapy udostępnia API punktów kurierskich oraz panel admina do zarządzania:

- Endpoints: `GET /api/map/points`, `GET /api/map/points/{id|code}` (wymaga nagłówka `X-API-Key`)
- Import punktów z CSV: `php artisan points:import path/to.csv --courier=inpost --type=parcel_locker --delimiter=; --header`
- Admin: `Admin -> Courier Points` (CRUD)
- Konfiguracja: `config/map.php` (tiles OSM, rate limits, cache)

#### Szybki start (migracje + seed)

1) Uruchom migracje i seedy:

```
php artisan migrate
php artisan db:seed --class=CourierPointsSeeder
```

2) Wygeneruj klucz API z zakresem `map.read` (np. w Tinker):

```
php artisan tinker
>>> $k = new App\\Models\\ApiKey(['key' => 'map_demo_key', 'scopes' => ['map.read'], 'status' => 'active']);
>>> $k->save();
```

3) Wywołania API (curl):

```
# Lista punktów w bbox (Warszawa), tylko InPost, paczkomaty
curl -H "X-API-Key: map_demo_key" \
  "http://185.213.25.106/api/map/points?bbox=52.0,20.8,52.5,21.3&courier_codes[]=inpost&types[]=parcel_locker&limit=100"

# Format GeoJSON
curl -H "X-API-Key: map_demo_key" \
  "http://185.213.25.106/api/map/points?bbox=52.0,20.8,52.5,21.3&format=geojson"

# Szczegóły punktu po kodzie
curl -H "X-API-Key: map_demo_key" \
  "http://185.213.25.106/api/map/points/WAW01234"
```

4) Panel admina: `admin/courier-points` (CRUD + import CSV).

---

### Instalacja i Uruchomienie

Aby uruchomić projekt lokalnie, postępuj zgodnie z poniższymi krokami:

**1. Wymagania wstępne:**
* PHP 8.2+
* Composer
* Node.js & NPM
* Serwer bazy danych MySQL

**2. Kroki instalacyjne:**

```bash
# 1. Sklonuj repozytorium
git clone [https://github.com/skyfly82/skybrokersystem.git](https://github.com/skyfly82/skybrokersystem.git)
cd skybrokersystem

# 2. Zainstaluj zależności PHP
composer install

# 3. Zainstaluj zależności JavaScript
npm install

# 4. Skopiuj plik konfiguracyjny .env
cp .env.example .env

# 5. Wygeneruj klucz aplikacji
php artisan key:generate

# 6. Uruchom migracje i seedery (jeśli istnieją)
# (Upewnij się, że skonfigurowałeś bazę danych w pliku .env)
php artisan migrate --seed

# 7. Skompiluj zasoby frontendowe
npm run dev
