# SkyBrokerSystem v6 - Architektura Systemu

## Przegląd Architektury

SkyBrokerSystem v6 został zrefaktoryzowany w ramach upgradu do Laravel 12 i implementuje architekturę Modular Monolith opartą na zasadach SOLID i wzorcach projektowych.

## Główne Komponenty

### 1. Warstwa Bezpieczeństwa

#### Uwierzytelnianie Multi-Guard
System wykorzystuje dwupoziomowe uwierzytelnianie Laravel:
- **System Users** (`system_user` guard): Administratorzy z rolami (admin, super_admin, marketing)
- **Customer Users** (`customer_user` guard): Użytkownicy korporacyjni przypisani do firm

#### Mechanizmy Bezpieczeństwa
- **CORS**: Skonfigurowane dla określonych domen frontend
- **Rate Limiting**: 60 żądań/minutę, 1000 żądań/godzinę dla API
- **Security Headers**: X-Frame-Options, CSP, XSS-Protection
- **API Keys**: Klucze z prefiksem `sk_` dla integracji zewnętrznych
- **Laravel Sanctum**: Tokeny API dla uwierzytelnionych użytkowników

### 2. Warstwa Usług (Service Layer)

#### Główne Usługi
```
app/Services/
├── Courier/                 # Integracje z kurierami
│   ├── CourierServiceInterface.php
│   └── Providers/
│       └── InPostService.php
├── Payment/                 # Przetwarzanie płatności  
│   ├── PaymentService.php
│   └── Providers/
│       ├── PayNowProvider.php
│       ├── StripeProvider.php
│       └── SimulationProvider.php
├── SMS/                     # Powiadomienia SMS
│   ├── SmsManager.php
│   └── Providers/
│       ├── TwilioProvider.php
│       ├── SmsApiProvider.php
│       └── LogProvider.php
└── Notification/            # System powiadomień
    └── NotificationService.php
```

#### Wzorce Projektowe
- **Service Container**: Dependency Injection dla wszystkich usług
- **Interface Segregation**: Dedykowane interfejsy dla każdego dostawcy
- **Factory Pattern**: CourierServiceFactory dla wyboru dostawcy
- **Strategy Pattern**: Różni dostawcy płatności/SMS

### 3. Routing Modularny

```
routes/
├── web.php              # Główne trasy web
├── api.php              # Główny routing API  
└── api/
    ├── auth.php         # Uwierzytelnianie API
    ├── couriers.php     # Endpointy kurierów
    ├── orders.php       # Zarządzanie zamówieniami
    └── payments.php     # Przetwarzanie płatności
```

### 4. Walidacja i Autoryzacja

#### Form Requests
- Wszystkie kontrolery wykorzystują dedykowane Form Requests
- Walidacja na poziomie żądania HTTP przed wykonaniem logiki biznesowej
- Przykład: `CreateShipmentRequest`, `UpdateCustomerRequest`

#### Policy-based Authorization
- `ShipmentPolicy`: Kontrola dostępu do przesyłek
- `OrderPolicy`: Autoryzacja operacji na zamówieniach  
- `ApiKeyPolicy`: Zarządzanie kluczami API

### 5. Middleware Stack

```
Global Middleware:
├── SecurityHeadersMiddleware    # Nagłówki bezpieczeństwa
├── RateLimitMiddleware         # Ograniczenia żądań
└── TrackUserActivity          # Audyt aktywności

Route-Specific:
├── AdminMiddleware            # Kontrola ról administratorów
├── CustomerActiveMiddleware   # Sprawdzenie statusu klienta
└── ApiKeyMiddleware          # Uwierzytelnianie kluczem API
```

## Baza Danych

### Struktura Tabel
- **UUID v7**: Wszystkie klucze główne używają UUID dla lepszej wydajności i bezpieczeństwa
- **Relacje**: Optymalizowane relacje z lazy loading i eager loading
- **Migracje**: Atomowe migracje z możliwością rollback

### Główne Modele
- `Customer`: Firmy z limitami kredytowymi i kluczami API
- `CustomerUser`: Użytkownicy przypisani do firm
- `Shipment`: Przesyłki z integracją kurierów
- `Payment`: Płatności z różnymi dostawcami
- `SystemUser`: Administratorzy z hierarchią ról

## API Design

### RESTful Endpoints
```
GET    /api/v1/health          # Status systemu
POST   /api/v1/auth/login      # Uwierzytelnianie  
GET    /api/v1/shipments       # Lista przesyłek
POST   /api/v1/shipments       # Utworzenie przesyłki
GET    /api/v1/payments        # Historia płatności
POST   /api/v1/payments        # Nowa płatność
```

### Response Format
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {},
    "meta": {
        "timestamp": "2025-09-02T12:00:00Z",
        "version": "6.0.0"
    }
}
```

## Konfiguracja Systemu

### Główny Plik Konfiguracji
`config/skybrokersystem.php` centralizuje wszystkie ustawienia:
- Dostawcy usług (kurierzy, płatności, SMS)
- Limity i timeouty
- Feature flags
- Ustawienia bezpieczeństwa

### Environment Variables
- Separacja konfiguracji dla różnych środowisk
- Bezpieczne przechowywanie kluczy API
- Ustawienia baz danych i cache

## Testing Strategy

### Smoke Tests
Podstawowe testy sprawdzające funkcjonalność:
- `HealthCheckTest`: Status endpointów i nagłówki bezpieczeństwa
- `AuthEndpointsTest`: Uwierzytelnianie i rate limiting

### Test Structure
```
tests/
├── Feature/
│   └── Api/
│       ├── HealthCheckTest.php
│       └── AuthEndpointsTest.php
└── Unit/
    └── Services/
```

## CI/CD Pipeline

### GitHub Actions
```yaml
on:
  push:
    branches: [ main, develop, 'refactor/*' ]
  pull_request:
    branches: [ main, develop ]

jobs:
  - PHP Matrix Testing (8.2, 8.3)
  - MySQL & Redis Services  
  - Code Style (Laravel Pint)
  - Security Audit (Composer)
  - PHPUnit Tests with Coverage
```

## Performance Optimizations

### Caching Strategy
- **Config Cache**: `php artisan config:cache` dla produkcji
- **Route Cache**: Optymalizacja routingu
- **View Cache**: Prekompilowane szablony Blade

### Database Optimizations  
- **Query Optimization**: Eager loading dla relacji
- **Index Strategy**: Indeksy na często wyszukiwane kolumny
- **Connection Pooling**: Optymalizacja połączeń z bazą

## Monitoring i Logging

### Audit Trail
- **AuditLog**: Śledzenie zmian w krytycznych danych
- **Activity Tracking**: Monitorowanie aktywności użytkowników
- **API Logs**: Szczegółowe logi wywołań API

### Error Handling
- **Centralized Exception Handling**: Spójne obsługiwanie błędów
- **Custom Exceptions**: Dedykowane wyjątki dla domenowych problemów
- **Logging Levels**: Różne poziomy logowania (debug, info, warning, error)

## Rozwój i Deployment

### Development Workflow
1. Feature branches z prefiksem `refactor/`
2. Code review przez Pull Requests
3. Automated testing w CI/CD
4. Manual QA testing
5. Deployment na staging/production

### Code Quality Standards
- **Laravel Pint**: Automatyczne formatowanie kodu
- **PHPStan**: Analiza statyczna kodu  
- **Security Audits**: Regularne skanowanie zależności

## Roadmap

### Planowane Ulepszenia
- [ ] Implementacja Event Sourcing
- [ ] Mikrousługi dla wybranych komponentów  
- [ ] GraphQL API jako alternatywa dla REST
- [ ] Advanced Monitoring (APM)
- [ ] Container Orchestration (Kubernetes)

### Migracja do Laravel 13
- Przygotowanie architektury pod przyszłe upgrady
- Separacja logiki biznesowej od frameworka
- Testowanie kompatybilności z nowymi wersjami PHP

---

**Wersja dokumentacji**: 6.0.0  
**Ostatnia aktualizacja**: 2025-09-02  
**Odpowiedzialny**: sky_fly82