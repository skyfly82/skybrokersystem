refactor.md
Nazwa systemu: skybrokersystem

Repozytorium: https://github.com/skyfly82/skybrokersystem

Data: 2025-09-02

Cel: Upgrade z Laravel 11 → 12 i refaktoryzacja do modularnego monolitu. Wprowadzenie architektury opartej na serwisach ("chude kontrolery"), wdrożenie zasad SOLID/DRY oraz wzmocnienie warstw bezpieczeństwa.

0) Główne założenia (TL;DR)
Aktualizacja: Podniesienie wersji frameworka z Laravel 11 do 12 wraz z niezbędnymi zależnościami.

Architektura: Logika biznesowa zostaje przeniesiona do serwisów w app/Services/** z interfejsami w app/Services/Contracts/**. Kontrolery pełnią jedynie rolę orkiestratorów.

Routing: Wprowadzenie routingu modułowego. Pliki tras umieszczone w routes/api/*.php (oraz opcjonalnie routes/api/v1/*.php) będą ładowane dynamicznie z poziomu bootstrap/app.php.

Bezpieczeństwo: Rygorystyczne stosowanie Form Requests do walidacji, Policies/Gates do autoryzacji, wdrożenie rate limitingu, konfiguracja CORS i dodanie nagłówków bezpieczeństwa.

Kluczowe zmiany w L12:

HasUuids domyślnie generuje UUID v7 (uporządkowane czasowo).

Domyślny katalog główny dla dysku local to teraz storage/app/private.

Reguła walidacji image nie zezwala na pliki SVG bez jawnego dopuszczenia.

Polecenie php artisan install:api automatycznie instaluje i konfiguruje Sanctum.

DatabaseTokenRepository::$expires przyjmuje wartości w sekundach (wcześniej w minutach).

1) Zakres prac
Upgrade L11→L12: Aktualizacja frameworka i zależności zgodnie z oficjalnym Upgrade Guide.

Backup: Stworzenie kopii zapasowej kluczowych katalogów w backup/YYYY-MM-DD/.

Nowa struktura: Implementacja nowej organizacji kodu (serwisy, kontrakty, routing modułowy).

Refaktoryzacja: Przeniesienie istniejącej logiki biznesowej z kontrolerów do dedykowanych serwisów.

Wzmocnienie bezpieczeństwa: Implementacja walidacji, autoryzacji, limitowania zapytań i innych mechanizmów ochronnych.

Baza danych: Przygotowanie nowych migracji i seederów odzwierciedlających docelowy model danych.

Dokumentacja: Aktualizacja docs/architecture.md i dodanie komentarzy nagłówkowych w modyfikowanych plikach.

2) Wymagania środowiskowe (bez Dockera)
PHP: ≥ 8.2 (zalecane 8.3)

Composer: 2.x

Rozszerzenia PHP: ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pcre, pdo, session, tokenizer, xml.

Baza danych: MySQL/MariaDB lub PostgreSQL.

Inne: Redis (zalecany dla cache i rate limitingu), Node.js ≥ 16 (dla Vite).

Uprawnienia: Serwer WWW musi mieć uprawnienia do zapisu w katalogach storage/ i bootstrap/cache/.

3) Plan migracji i rollback (Git)
Gałąź robocza: Wszystkie zmiany będą wprowadzane na gałęzi refactor/laravel12-2025-09-02.

Backup: Przed rozpoczęciem prac, kluczowe katalogi (app, routes, database itp.) zostaną skopiowane do backup/2025-09-02/.

Rollback: W razie problemów, powrót do stanu początkowego nastąpi poprzez git reset --hard do ostatniego stabilnego commita.

4) Docelowa organizacja kodu i komentarze
Założenia: Logika domenowa jest w pełni odizolowana w serwisach. Kontrolery są "chude" i odpowiedzialne wyłącznie za obsługę żądań HTTP.

Komentarz nagłówkowy: Każdy nowo utworzony lub zmodyfikowany plik PHP musi zawierać poniższy nagłówek.

PHP

<?php

/**
 * Cel: [Krótki i zwięzły opis przeznaczenia pliku]
 * Moduł: [np. Orders, Couriers]
 * Odpowiedzialny: [Claude-Code]
 * Data: 2025-09-02
 */
5) Dziennik działań – Krok po kroku dla Claude-Code
Założenia operacyjne:

claude-code ma dostęp do systemu plików na serwerze i repozytorium skybrokersystem.

Żadna struktura nie jest tworzona z góry. AI tworzy wszystkie niezbędne pliki i katalogi samodzielnie, wykonując poniższe polecenia.

Wszystkie polecenia są idempotentne (ich wielokrotne wykonanie nie powoduje błędu).

[KROK 1] Przygotowanie środowiska i repozytorium
Sklonuj repozytorium (jeśli nie istnieje lokalnie).

Przejdź do katalogu projektu.

Upewnij się, że gałąź main jest aktualna.

Utwórz nową gałąź roboczą.

[KROK 2] Backup istniejącego kodu
Utwórz katalog na backup.

Skopiuj kluczowe katalogi (app, routes, database, resources, tests, config) do folderu backupu.

Zatwierdź zmiany commitem.

[KROK 3] Aktualizacja zależności do Laravel 12
Zmodyfikuj composer.json, podnosząc wersje laravel/framework, sanctum, phpunit/pest i pint.

Wyczyść cache composera i zaktualizuj zależności.

Zweryfikuj instalację za pomocą php artisan about i composer diagnose.

Zatwierdź zmiany.

[KROK 4] Implementacja routingu modułowego
Utwórz plik routes/api.php jako główny punkt wejścia dla API (może zawierać np. endpoint /health).

Utwórz katalog routes/api/, a w nim pliki dla poszczególnych modułów (np. orders.php, couriers.php).

Zmodyfikuj bootstrap/app.php, dodając logikę, która dynamicznie załaduje wszystkie pliki tras z katalogu routes/api/ (oraz opcjonalnie routes/api/v*/**).

Zatwierdź zmiany.

[KROK 5] Budowa warstwy serwisów i "chudych" kontrolerów
Utwórz strukturę katalogów app/Services/Contracts/<Moduł> oraz app/Services/<Moduł>.

W tych katalogach utwórz pliki z interfejsami (np. OrderServiceInterface.php) i ich implementacjami (np. OrderService.php).

Utwórz kontrolery (np. app/Http/Controllers/Api/Orders/OrderController.php), które wstrzykują serwisy przez konstruktor.

Zarejestruj powiązania interfejsów z implementacjami w AppServiceProvider.

Zatwierdź zmiany.

[KROK 6] Implementacja walidacji i autoryzacji
Utwórz dedykowane Form Requests (np. StoreOrderRequest.php) dla operacji zapisu i aktualizacji.

Utwórz Policies (np. OrderPolicy.php) i powiąż je z odpowiednimi modelami.

Zastosuj ->can(...) w definicjach tras lub authorize() w Form Requests.

Zatwierdź zmiany.

[KROK 7] Konfiguracja warstw bezpieczeństwa
Zainstaluj Sanctum za pomocą php artisan install:api.

Skonfiguruj CORS w config/cors.php.

Zdefiniuj Rate Limiting dla API w AppServiceProvider.

Dodaj middleware dla nagłówków bezpieczeństwa (X-Frame-Options, CSP itp.).

W config/filesystems.php jawnie ustaw ścieżkę dla dysku local na storage_path('app'), jeśli poprzednia logika tego wymagała (domyślnie w L12 jest to storage/app/private).

Zatwierdź zmiany.

[KROK 8] Aktualizacja bazy danych
Zmodyfikuj lub utwórz migracje, upewniając się, że używają UUID (domyślnie v7) dla kluczy głównych modeli z HasUuids.

Utwórz seedery z danymi testowymi.

Uruchom migracje i seedery lokalnie (php artisan migrate:fresh --seed).

Zatwierdź zmiany.

[KROK 9] Testy, jakość kodu i CI
Dodaj plik konfiguracyjny .pint.json.

Utwórz testy typu "smoke" dla kluczowych endpointów API (sprawdzające kody odpowiedzi 2xx, 4xx, 429).

Skonfiguruj workflow GitHub Actions do uruchamiania testów i pint na gałęziach main i PR.

Zatwierdź zmiany.

[KROK 10] Aktualizacja dokumentacji i przygotowanie PR
Uzupełnij plik docs/architecture.md o opis nowej architektury.

Upewnij się, że wszystkie nowe pliki mają komentarze nagłówkowe.

Zatwierdź zmiany.

Wygeneruj raport (route:list, about) i utwórz Pull Request do gałęzi main.

Zasady dla claude-code (Rozszerzone)
Brak założeń: Nie twórz żadnych plików ani katalogów, które nie są jawnie wymienione w krokach. Jeśli czegoś brakuje, zgłoś to.

Idempotencja: Wszystkie skrypty i polecenia muszą być bezpieczne do wielokrotnego uruchomienia.

Backup na początku: Modyfikowane katalogi (app, routes itd.) muszą być najpierw skopiowane do backup/YYYY-MM-DD/.

Praca na gałęzi: Cała praca odbywa się na gałęzi refactor/laravel12-YYYY-MM-DD. Używaj małych, opisowych commitów zgodnych z Conventional Commits.

Chude kontrolery: Cała logika biznesowa musi znajdować się w app/Services/**. Kontrolery służą tylko do obsługi HTTP.

Bezpieczeństwo jest kluczowe: Wymuszaj stosowanie Form Requests, Policies, rate limitera api, CORS i bezpiecznej konfiguracji filesystems.local.

Testy i jakość: Przed każdym git push uruchamiaj testy i pint. Build CI musi być "zielony".

Środowisko: Pracuj w natywnym środowisku systemowym, bez użycia Dockera.

