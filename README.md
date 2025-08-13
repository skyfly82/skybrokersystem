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